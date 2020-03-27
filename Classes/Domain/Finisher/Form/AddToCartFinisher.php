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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
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
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param CartUtility $cartUtility
     */
    public function injectCartUtility(
        CartUtility $cartUtility
    ) {
        $this->cartUtility = $cartUtility;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    protected function executeInternal()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->pluginSettings);

        $formValues = $this->getFormValues();

        $className = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart'][$formValues['productType']]['Form']['AddToCartFinisher'];
        $hookObject = GeneralUtility::makeInstance($className);
        if (!$hookObject instanceof AddToCartFinisherInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . AddToCartFinisherInterface::class, 123);
        }

        unset($formValues[$this->getHoneypotIdentifier()]);

        list($errors, $cartProducts) = $hookObject->getProductFromForm(
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
            $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK;

            if (isset($_GET['type'])) {
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
                    \TYPO3\CMS\Core\Messaging\FlashMessage::class,
                    (string)$messageBody,
                    (string)$messageTitle,
                    $severity,
                    true
                );

                $this->finisherContext->getControllerContext()->getFlashMessageQueue()->addMessage($flashMessage);
            }
        }
    }

    /**
     * Returns the values of the submitted form
     *
     * @return array
     */
    protected function getFormValues(): array
    {
        return $this->finisherContext->getFormValues();
    }

    /**
     * @return string
     */
    protected function getHoneypotIdentifier()
    {
        foreach ($this->finisherContext->getFormRuntime()->getFormDefinition()->getRenderablesRecursively() as $renderable) {
            if ($renderable->getType() === 'Honeypot') {
                return $renderable->getIdentifier();
            }
        }
        return '';
    }

    /**
     * @param array $products
     * @return int
     */
    protected function addProductsToCart($products)
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

    /**
     * @param array $formValues
     * @param string $status
     * @return string|null
     */
    protected function getStatusMessageBody(array $formValues, string $status = '200')
    {
        $messageBody = LocalizationUtility::translate(
            'tx_cart.add_to_cart_finisher.' . $formValues['productType'] . '.message.status.' . $status . '.body',
            'cart'
        );
        if ($messageBody === null) {
            $messageBody = LocalizationUtility::translate(
                'tx_cart.add_to_cart_finisher.message.status.' . $status . '.body',
                'cart'
            );
        }
        return $messageBody;
    }

    /**
     * @param array $formValues
     * @param string $status
     * @return string|null
     */
    protected function getStatusMessageTitle(array $formValues, string $status = '200')
    {
        $messageTitle = LocalizationUtility::translate(
            'tx_cart.add_to_cart_finisher.' . $formValues['productType'] . '.message.status.' . $status . '.title',
            'cart'
        );
        if ($messageTitle === null) {
            $messageTitle = LocalizationUtility::translate(
                'tx_cart.add_to_cart_finisher.message.status.' . $status . '.title',
                'cart'
            );
        }
        return $messageTitle;
    }
}
