<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Domain\Validator\OrderItemValidator;
use Extcode\Cart\Event\Order\CreateEvent;
use Extcode\Cart\Event\Order\FinishEvent;
use Extcode\Cart\Event\Order\NumberGeneratorEvent;
use Extcode\Cart\Event\Order\PaymentEvent;
use Extcode\Cart\Event\Order\StockEvent;
use Extcode\Cart\Event\ProcessOrderCheckStockEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

class OrderController extends ActionController
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    protected function getErrorFlashMessage()
    {
        $errorMsg = LocalizationUtility::translate(
            'tx_cart.error.validation',
            'Cart'
        );

        return $errorMsg;
    }

    /**
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher = null)
    {
        if ($eventDispatcher !== null) {
            $this->eventDispatcher = $eventDispatcher;
        }
    }

    public function initializeCreateAction()
    {
        foreach (['orderItem', 'billingAddress', 'shippingAddress'] as $argumentName) {
            if (!$this->arguments->hasArgument($argumentName)) {
                continue;
            }
            if (isset($this->settings['validation'][$argumentName]['fields'])) {
                $fields = $this->settings['validation'][$argumentName]['fields'];

                foreach ($fields as $propertyName => $validatorConf) {
                    $this->setDynamicValidation(
                        $argumentName,
                        $propertyName,
                        [
                            'validator' => $validatorConf['validator'],
                            'options' => is_array($validatorConf['options'] ?? null)
                                ? $validatorConf['options']
                                : []
                        ]
                    );
                }
            }
        }

        if ($this->arguments->hasArgument('orderItem')) {
            $this->arguments->getArgument('orderItem')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
        if ($this->arguments->hasArgument('billingAddress')) {
            $this->arguments->getArgument('billingAddress')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
        if ($this->arguments->hasArgument('shippingAddress')) {
            $this->arguments->getArgument('shippingAddress')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
    }

    /**
     * Action order Cart
     *
     * @param Item $orderItem
     * @param BillingAddress $billingAddress
     * @param ShippingAddress $shippingAddress
     *
     * @IgnoreValidation("shippingAddress")
     */
    public function createAction(
        Item $orderItem = null,
        BillingAddress $billingAddress = null,
        ShippingAddress $shippingAddress = null
    ) {
        if (is_null($billingAddress)) {
            $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'cart_billing_address_' . $this->settings['cart']['pid']);
            $billingAddress = unserialize($sessionData);
        } else {
            $sessionData = serialize($billingAddress);
            $GLOBALS['TSFE']->fe_user->setKey('ses', 'cart_billing_address_' . $this->settings['cart']['pid'], $sessionData);
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        }
        if (is_null($shippingAddress)) {
            $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'cart_shipping_address_' . $this->settings['cart']['pid']);
            $shippingAddress = unserialize($sessionData);
        } else {
            $sessionData = serialize($shippingAddress);
            $GLOBALS['TSFE']->fe_user->setKey('ses', 'cart_shipping_address_' . $this->settings['cart']['pid'], $sessionData);
            $GLOBALS['TSFE']->fe_user->storeSessionData();
        }

        if (($orderItem === null) || ($billingAddress === null)) {
            $this->redirect('show', 'Cart\Cart');
        }

        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        if ($this->cart->getCount() === 0) {
            $this->redirect('show', 'Cart\Cart');
        }

        $this->eventDispatcher->dispatch(new ProcessOrderCheckStockEvent($this->cart));

        $orderItem->setCartPid(intval($GLOBALS['TSFE']->id));

        // add billing and shipping address to order

        $storagePid = $this->pluginSettings['settings']['order']['pid'];
        $billingAddress->setPid($storagePid);
        $orderItem->setBillingAddress($billingAddress);

        if ($orderItem->isShippingSameAsBilling()) {
            $shippingAddress = null;
            $orderItem->removeShippingAddress();
        } else {
            $shippingAddress->setPid($storagePid);
            $orderItem->setShippingAddress($shippingAddress);
        }

        $payment = $this->cart->getPayment();
        if (method_exists($payment, 'getProvider')) {
            $provider = $payment->getProvider();
        }

        $isPropagationStopped = $this->dispatchOrderCreateEvents($orderItem);

        if ($isPropagationStopped) {
            return;
        }

        $this->view->assign('cart', $this->cart);
        $this->view->assign('orderItem', $orderItem);

        $paymentId = $this->cart->getPayment()->getId();
        $paymentSettings = $this->parserUtility->getTypePluginSettings($this->pluginSettings, $this->cart, 'payments');

        if (isset($paymentSettings['options'][$paymentId]['redirects']['success']['url'])) {
            $this->redirectToUri($paymentSettings['options'][$paymentId]['redirects']['success']['url'], 0, 200);
        }
    }

    /**
     * @param Item $orderItem
     */
    public function showAction(Item $orderItem)
    {
        $this->view->assign('orderItem', $orderItem);
    }

    /**
     * Sets the dynamic validation rules.
     *
     * @param string $argumentName
     * @param string $propertyName
     * @param array $validatorConf
     * @throws NoSuchValidatorException
     */
    protected function setDynamicValidation($argumentName, $propertyName, $validatorConf)
    {
        // build custom validation chain
        /** @var ValidatorResolver $validatorResolver */
        $validatorResolver = GeneralUtility::makeInstance(
            ValidatorResolver::class
        );

        if ($validatorConf['validator'] === 'Empty') {
            $validatorConf['validator'] = '\Extcode\Cart\Validation\Validator\EmptyValidator';
        }

        $propertyValidator = $validatorResolver->createValidator(
            $validatorConf['validator'],
            $validatorConf['options']
        );

        if ($argumentName === 'orderItem') {
            /** @var OrderItemValidator $modelValidator */
            $modelValidator = $validatorResolver->createValidator(
                OrderItemValidator::class
            );
        } else {
            /** @var \TYPO3\CMS\Extbase\Validation\Validator\GenericObject $modelValidator */
            $modelValidator = $validatorResolver->createValidator('GenericObject');
        }

        $modelValidator->addPropertyValidator(
            $propertyName,
            $propertyValidator
        );

        /** @var ConjunctionValidator $conjunctionValidator */
        $conjunctionValidator = $this->arguments->getArgument($argumentName)->getValidator();
        if ($conjunctionValidator === null) {
            $conjunctionValidator = $validatorResolver->createValidator(
                ConjunctionValidator::class
            );
            $this->arguments->getArgument($argumentName)->setValidator($conjunctionValidator);
        }
        $conjunctionValidator->addValidator($modelValidator);
    }

    protected function dispatchOrderCreateEvents(Item $orderItem): bool
    {
        $createEvent = new CreateEvent($this->cart, $orderItem, $this->pluginSettings);
        $this->eventDispatcher->dispatch($createEvent);
        if ($createEvent instanceof StoppableEventInterface && $createEvent->isPropagationStopped()) {
            return true;
        }

        $onlyGenerateNumberOfType = [];
        if (!empty($this->pluginSettings['autoGenerateNumbers'])) {
            $onlyGenerateNumberOfType = array_map('trim', explode(',', $this->pluginSettings['autoGenerateNumbers']));
        }
        $generateNumbersEvent = new NumberGeneratorEvent($this->cart, $orderItem, $this->pluginSettings);
        $generateNumbersEvent->setOnlyGenerateNumberOfType($onlyGenerateNumberOfType);
        $this->eventDispatcher->dispatch($generateNumbersEvent);
        if ($generateNumbersEvent instanceof StoppableEventInterface && $generateNumbersEvent->isPropagationStopped()) {
            return true;
        }

        $stockEvent = new StockEvent($this->cart, $orderItem, $this->pluginSettings);
        $this->eventDispatcher->dispatch($stockEvent);
        if ($stockEvent instanceof StoppableEventInterface && $stockEvent->isPropagationStopped()) {
            return true;
        }

        $paymentEvent = new PaymentEvent($this->cart, $orderItem, $this->pluginSettings);
        $this->eventDispatcher->dispatch($paymentEvent);
        if ($paymentEvent instanceof StoppableEventInterface && $paymentEvent->isPropagationStopped()) {
            return true;
        }

        $finishEvent = new FinishEvent($this->cart, $orderItem, $this->pluginSettings);
        $this->eventDispatcher->dispatch($finishEvent);
        if ($finishEvent instanceof StoppableEventInterface && $finishEvent->isPropagationStopped()) {
            return true;
        }

        return false;
    }
}
