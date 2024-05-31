<?php

declare(strict_types=1);

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
use Extcode\Cart\Service\PaymentMethodsServiceInterface;
use Extcode\Cart\Validation\Validator\EmptyValidator;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Annotation\IgnoreValidation;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractGenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator;
use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

class OrderController extends ActionController
{
    public function __construct(
        private readonly PaymentMethodsServiceInterface $paymentMethodsService
    ) {}

    protected function getErrorFlashMessage()
    {
        return LocalizationUtility::translate(
            'tx_cart.error.validation',
            'Cart'
        );
    }

    public function initializeCreateAction(): void
    {
        foreach (['orderItem', 'billingAddress', 'shippingAddress'] as $argumentName) {
            if (!$this->arguments->hasArgument($argumentName)) {
                continue;
            }
            $this->setDynamicValidationsForArgument($argumentName);

            $this->arguments->getArgument($argumentName)
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
    }

    /**
     * @IgnoreValidation("shippingAddress")
     */
    public function createAction(
        Item $orderItem = null,
        BillingAddress $billingAddress = null,
        ShippingAddress $shippingAddress = null
    ): ResponseInterface {
        $this->restoreSession();

        if (!is_null($billingAddress)) {
            $this->sessionHandler->writeAddress('billing_address_' . $this->settings['cart']['pid'], $billingAddress);
        } else {
            $billingAddress = $this->sessionHandler->restoreAddress('billing_address_' . $this->settings['cart']['pid']);
        }
        if (!is_null($shippingAddress)) {
            $this->sessionHandler->writeAddress('shipping_address_' . $this->settings['cart']['pid'], $shippingAddress);
        } else {
            $shippingAddress = $this->sessionHandler->restoreAddress('shipping_address_' . $this->settings['cart']['pid']);
            if (!$shippingAddress) {
                $shippingAddress = new ShippingAddress();
            }
        }

        if (is_null($orderItem) || is_null($billingAddress) || $this->cart->getCount() === 0) {
            return $this->redirect('show', 'Cart\Cart');
        }

        $processOrderCheckStockEvent = new ProcessOrderCheckStockEvent($this->cart);
        $this->eventDispatcher->dispatch($processOrderCheckStockEvent);

        if (!$processOrderCheckStockEvent->isEveryProductAvailable()) {
            $insufficientStockMessages = $processOrderCheckStockEvent->getInsufficientStockMessages();

            foreach ($insufficientStockMessages as $insufficientStockMessage) {
                $insufficientStockMessage->setStoreInSession(true);
                $this->getFlashMessageQueue()->enqueue($insufficientStockMessage);
            }

            return $this->redirect('show', 'Cart\Cart');
        }

        $orderItem->setCartPid((int)$this->settings['cart']['pid']);

        // add billing and shipping address to order
        $storagePid = (int)$this->settings['order']['pid'];
        $billingAddress->setPid($storagePid);
        $orderItem->setBillingAddress($billingAddress);

        if ($orderItem->isShippingSameAsBilling()) {
            $shippingAddress = null;
            $orderItem->removeShippingAddress();
        } else {
            $shippingAddress->setPid($storagePid);
            $orderItem->setShippingAddress($shippingAddress);
        }

        $isPropagationStopped = $this->dispatchOrderCreateEvents($orderItem);

        if ($isPropagationStopped) {
            // @todo Check the Response Type
            return $this->htmlResponse();
        }

        $this->view->assign('cart', $this->cart);
        $this->view->assign('orderItem', $orderItem);

        $paymentId = $this->cart->getPayment()->getId();
        $paymentSettings = $this->paymentMethodsService->getConfigurationsForType('payments', $this->cart->getBillingCountry());

        if (isset($paymentSettings['options'][$paymentId]['redirects']['success']['url'])) {
            $this->redirectToUri($paymentSettings['options'][$paymentId]['redirects']['success']['url'], 0, 200);
        }

        return $this->htmlResponse();
    }

    public function showAction(Item $orderItem): ResponseInterface
    {
        $this->view->assign('orderItem', $orderItem);

        return $this->htmlResponse();
    }

    public function setDynamicValidationsForArgument(string $argumentName): void
    {
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
                            : [],
                    ]
                );
            }
        }
    }

    /**
     * Sets the dynamic validation rules.
     */
    protected function setDynamicValidation(
        string $argumentName,
        string $propertyName,
        array $validatorConf
    ): void {
        // build custom validation chain
        /** @var ValidatorResolver $validatorResolver */
        $validatorResolver = GeneralUtility::makeInstance(
            ValidatorResolver::class
        );

        if ($validatorConf['validator'] === 'Empty') {
            $validatorConf['validator'] = EmptyValidator::class;
        }

        $propertyValidator = $validatorResolver->createValidator(
            $validatorConf['validator'],
            $validatorConf['options']
        );

        if ($argumentName === 'orderItem') {
            $modelValidator = $validatorResolver->createValidator(OrderItemValidator::class);
        } else {
            $modelValidator = $validatorResolver->createValidator(GenericObjectValidator::class);
        }

        if (!$modelValidator instanceof AbstractGenericObjectValidator) {
            return;
        }

        $modelValidator->addPropertyValidator(
            $propertyName,
            $propertyValidator
        );

        $conjunctionValidator = $this->arguments->getArgument($argumentName)->getValidator();
        if ($conjunctionValidator instanceof ConjunctionValidator) {
            $conjunctionValidator->addValidator($modelValidator);
        }
    }

    protected function dispatchOrderCreateEvents(Item $orderItem): bool
    {
        $createEvent = new CreateEvent($this->cart, $orderItem, $this->configurations);
        $this->eventDispatcher->dispatch($createEvent);
        if ($createEvent instanceof StoppableEventInterface && $createEvent->isPropagationStopped()) {
            return true;
        }

        $onlyGenerateNumberOfType = [];
        if (!empty($this->configurations['autoGenerateNumbers'])) {
            $onlyGenerateNumberOfType = array_map('trim', explode(',', $this->configurations['autoGenerateNumbers']));
        }
        $generateNumbersEvent = new NumberGeneratorEvent($this->cart, $orderItem, $this->configurations);
        $generateNumbersEvent->setOnlyGenerateNumberOfType($onlyGenerateNumberOfType);
        $this->eventDispatcher->dispatch($generateNumbersEvent);
        if ($generateNumbersEvent instanceof StoppableEventInterface && $generateNumbersEvent->isPropagationStopped()) {
            return true;
        }

        $stockEvent = new StockEvent($this->cart, $orderItem, $this->configurations);
        $this->eventDispatcher->dispatch($stockEvent);
        if ($stockEvent instanceof StoppableEventInterface && $stockEvent->isPropagationStopped()) {
            return true;
        }

        $paymentEvent = new PaymentEvent($this->cart, $orderItem, $this->configurations);
        $this->eventDispatcher->dispatch($paymentEvent);
        if ($paymentEvent instanceof StoppableEventInterface && $paymentEvent->isPropagationStopped()) {
            return true;
        }

        $finishEvent = new FinishEvent($this->cart, $orderItem, $this->configurations);
        $this->eventDispatcher->dispatch($finishEvent);
        if ($finishEvent instanceof StoppableEventInterface && $finishEvent->isPropagationStopped()) {
            return true;
        }

        return false;
    }
}
