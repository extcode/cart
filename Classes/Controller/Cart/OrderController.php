<?php

namespace Extcode\Cart\Controller\Cart;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Cart Order Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class OrderController extends ActionController
{
    /**
     * Stock Utility
     *
     * @var \Extcode\Cart\Utility\StockUtility
     */
    protected $stockUtility;

    /**
     * @param \Extcode\Cart\Utility\StockUtility $stockUtility
     */
    public function injectStockUtility(
        \Extcode\Cart\Utility\StockUtility $stockUtility
    ) {
        $this->stockUtility = $stockUtility;
    }

    /**
     * @return string
     */
    protected function getErrorFlashMessage()
    {
        $getValidationResults = $this->arguments->getValidationResults();

        if ($getValidationResults->hasErrors()) {
            $errorMsg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'tx_cart.error.validation',
                $this->extensionName
            );

            return $errorMsg;
        }

        $errorMsg = parent::getErrorFlashMessage();

        return $errorMsg;
    }

    public function initializeCreateAction()
    {
        foreach (['orderItem', 'billingAddress', 'shippingAddress'] as $argumentName) {
            if (!$this->arguments->hasArgument($argumentName)) {
                continue;
            }
            if ($this->settings['validation'] &&
                $this->settings['validation'][$argumentName] &&
                $this->settings['validation'][$argumentName]['fields']
            ) {
                $fields = $this->settings['validation'][$argumentName]['fields'];

                foreach ($fields as $propertyName => $validatorConf) {
                    $this->setDynamicValidation(
                        $argumentName,
                        $propertyName,
                        [
                            'validator' => $validatorConf['validator'],
                            'options' => is_array($validatorConf['options'])
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
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     *
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation $shippingAddress
     */
    public function createAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (($orderItem == null) || ($billingAddress == null)) {
            $this->redirect('show', 'Cart\Cart');
        }

        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        if ($this->cart->getCount() == 0) {
            $this->redirect('show', 'Cart\Cart');
        }

        $this->stockUtility->checkStock($this->cart, $this->pluginSettings);

        $orderItem->setCartPid(intval($GLOBALS['TSFE']->id));

        // add billing and shipping address to order

        $storagePid = $this->pluginSettings['settings']['order']['pid'];
        $billingAddress->setPid($storagePid);
        $orderItem->setBillingAddress($billingAddress);

        if ($this->request->hasArgument('shipping_same_as_billing')) {
            $useSameAddress = $this->request->getArgument('shipping_same_as_billing');

            if ($useSameAddress === 'true') {
                $shippingAddress = null;
                $orderItem->removeShippingAddress();
            } else {
                $shippingAddress->setPid($storagePid);
                $orderItem->setShippingAddress($shippingAddress);
            }
        }

        $this->invokeFinishers($orderItem);

        $this->view->assign('cart', $this->cart);
        $this->view->assign('orderItem', $orderItem);

        $paymentId = $this->cart->getPayment()->getId();
        $paymentSettings = $this->parserUtility->getTypePluginSettings($this->pluginSettings, $this->cart, 'payments');

        if ($paymentSettings['options'][$paymentId] &&
            $paymentSettings['options'][$paymentId]['redirects'] &&
            $paymentSettings['options'][$paymentId]['redirects']['success'] &&
            $paymentSettings['options'][$paymentId]['redirects']['success']['url']
        ) {
            $this->redirectToUri($paymentSettings['options'][$paymentId]['redirects']['success']['url'], 0, 200);
        }
    }

    /**
     * Executes all finishers of this form
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     */
    protected function invokeFinishers(\Extcode\Cart\Domain\Model\Order\Item $orderItem)
    {
        $finisherContext = $this->objectManager->get(
            \Extcode\Cart\Domain\Finisher\FinisherContext::class,
            $this->pluginSettings,
            $this->cart,
            $orderItem,
            $this->getControllerContext()
        );

        if (is_array($this->pluginSettings['finishers']) &&
            is_array($this->pluginSettings['finishers']['order'])
        ) {
            ksort($this->pluginSettings['finishers']['order']);
            foreach ($this->pluginSettings['finishers']['order'] as $finisherConfig) {
                $finisherClass = $finisherConfig['class'];

                if (class_exists($finisherClass)) {
                    $finisher = $this->objectManager->get($finisherClass);
                    $finisher->execute($finisherContext);
                    if ($finisherContext->isCancelled()) {
                        break;
                    }
                } else {
                    $logManager = $this->objectManager->get(
                        \TYPO3\CMS\Core\Log\LogManager::class
                    );
                    $logger = $logManager->getLogger(__CLASS__);
                    $logger->error('Can\'t find Finisher class \'' . $finisherClass . '\'.', []);
                }
            }
        }
    }

    /**
     * Sets the dynamic validation rules.
     *
     * @param string $argumentName
     * @param string $propertyName
     * @param array $validatorConf
     * @throws \TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException
     */
    protected function setDynamicValidation($argumentName, $propertyName, $validatorConf)
    {
        // build custom validation chain
        /** @var \TYPO3\CMS\Extbase\Validation\ValidatorResolver $validatorResolver */
        $validatorResolver = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Validation\ValidatorResolver::class
        );

        if ($validatorConf['validator'] == 'Empty') {
            $validatorConf['validator'] = '\Extcode\Cart\Validation\Validator\EmptyValidator';
        }

        $propertyValidator = $validatorResolver->createValidator(
            $validatorConf['validator'],
            $validatorConf['options']
        );

        if ($argumentName === 'orderItem') {
            /** @var \Extcode\Cart\Domain\Validator\OrderItemValidator $modelValidator */
            $modelValidator = $validatorResolver->createValidator(
                \Extcode\Cart\Domain\Validator\OrderItemValidator::class
            );
        } else {
            /** @var \TYPO3\CMS\Extbase\Validation\Validator\GenericObject $modelValidator */
            $modelValidator = $validatorResolver->createValidator('GenericObject');
        }

        $modelValidator->addPropertyValidator(
            $propertyName,
            $propertyValidator
        );

        /** @var \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator $conjunctionValidator */
        $conjunctionValidator = $this->arguments->getArgument($argumentName)->getValidator();
        if ($conjunctionValidator === null) {
            $conjunctionValidator = $validatorResolver->createValidator(
                \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator::class
            );
            $this->arguments->getArgument($argumentName)->setValidator($conjunctionValidator);
        }
        $conjunctionValidator->addValidator($modelValidator);
    }
}
