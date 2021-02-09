<?php

namespace Extcode\Cart\Domain\Finisher\Form;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Utility\CartUtility;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Form\Domain\Finishers\AbstractFinisher;

class AddToCartFinisher extends AbstractFinisher
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var CartUtility
     */
    protected $cartUtility;

    /**
     * @var array
     */
    protected $pluginSettings;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @param CartUtility $cartUtility
     */
    public function injectCartUtility(CartUtility $cartUtility): void
    {
        $this->cartUtility = $cartUtility;
    }

    /**
     * @param ConfigurationManager $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManager $configurationManager): void
    {
        $this->configurationManager = $configurationManager;
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    protected function executeInternal(): void
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->pluginSettings);

        $formValues = $this->getFormValues();

        $className = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$formValues['productType']]['Form']['AddToCartFinisher'];
        $hookObject = GeneralUtility::makeInstance($className);
        if (!$hookObject instanceof AddToCartFinisherInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . AddToCartFinisherInterface::class, 123);
        }

        unset($formValues[$this->getHoneypotIdentifier()]);

        [$errors, $cartProducts] = $hookObject->getProductFromForm(
            $formValues,
            $this->cart
        );

        if (empty($errors)) {
            $quantity = $this->addProductsToCart($cartProducts);

            $this->cartUtility->updateService($this->cart, $this->pluginSettings);

            $this->cartUtility->writeCartToSession($this->cart, $this->pluginSettings['settings']);

            $status = '200';
            $messageBody = $this->getStatusMessageBody($formValues, $status);
            $messageTitle = $this->getStatusMessageTitle($formValues);
            $severity = AbstractMessage::OK;

            $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
            if (in_array((int)$pageType, $this->pluginSettings['settings']['jsonResponseForPageTypes'])) {
                $response = [
                    'status' => $status,
                    'added' => $quantity,
                    'count' => $this->cart->getCount(),
                    'net' => $this->cart->getNet(),
                    'gross' => $this->cart->getGross(),
                    'messageBody' => $messageBody,
                    'messageTitle' => $messageTitle,
                    'severity' => $severity
                ];

                $this->finisherContext->getFormRuntime()->getResponse()->setContent(json_encode($response));
            } else {
                $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    (string)$messageBody,
                    (string)$messageTitle,
                    $severity,
                    true
                );

                $this->finisherContext->getControllerContext()->getFlashMessageQueue()->addMessage($flashMessage);
            }
        }
    }

    protected function getFormValues(): array
    {
        return $this->finisherContext->getFormValues();
    }

    protected function getHoneypotIdentifier(): string
    {
        foreach ($this->finisherContext->getFormRuntime()->getFormDefinition()->getRenderablesRecursively() as $renderable) {
            if ($renderable->getType() === 'Honeypot') {
                return $renderable->getIdentifier();
            }
        }
        return '';
    }

    protected function addProductsToCart(array $products): int
    {
        $quantity = 0;

        foreach ($products as $product) {
            if ($product instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
                $quantity += $product->getQuantity();
                $this->cart->addProduct($product);
            }
        }
        return $quantity;
    }

    protected function getStatusMessageBody(array $formValues, $status = '200'): ?string
    {
        $messageBody = LocalizationUtility::translate(
            'tx_cart.add_to_cart_finisher.' . $formValues['productType'] . '.message.status.' . $status . '.body',
            'Cart'
        );
        if ($messageBody === null) {
            $messageBody = LocalizationUtility::translate(
                'tx_cart.add_to_cart_finisher.message.status.' . $status . '.body',
                'Cart'
            );
        }
        return $messageBody;
    }

    protected function getStatusMessageTitle(array $formValues, $status = '200'): ?string
    {
        $messageTitle = LocalizationUtility::translate(
            'tx_cart.add_to_cart_finisher.' . $formValues['productType'] . '.message.status.' . $status . '.title',
            'Cart'
        );
        if ($messageTitle === null) {
            $messageTitle = LocalizationUtility::translate(
                'tx_cart.add_to_cart_finisher.message.status.' . $status . '.title',
                'Cart'
            );
        }
        return $messageTitle;
    }
}
