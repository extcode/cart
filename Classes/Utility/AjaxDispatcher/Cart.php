<?php

namespace Extcode\Cart\Utility\AjaxDispatcher;

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
 * AjaxDispatcher Cart
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Cart
{
    /**
     * @var array
     */
    protected $conf;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var string
     */
    protected $extensionName;

    /**
     * @var string
     */
    protected $pluginName;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * Array of all request Arguments
     *
     * @var array
     */
    protected $requestArguments = array();

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $arguments = array();

    /**
     * Cart Utility
     *
     * @var \Extcode\Cart\Utility\CartUtility
     */
    protected $cartUtility;

    /**
     * Parser Utility
     *
     * @var \Extcode\Cart\Utility\ParserUtility
     */
    protected $parserUtility;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Prepare the call arguments
     *
     * @return \Extcode\Cart\Utility\AjaxDispatcher\Cart
     */
    public function initCallArguments()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Extbase\Object\ObjectManager'
        );

        $ajax = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('request');

        $this->request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Request');
        $this->request->setControllerVendorName('Extcode');
        $this->request->setcontrollerExtensionName('Cart');
        $this->request->setPluginName('Cart');
        $this->request->setControllerName('Cart');
        $this->request->setControllerActionName('addProduct');
        $this->request->setArguments($ajax['arguments']);

        /**
         * @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService
         */
        $typoScriptService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Service\\TypoScriptService'
        );
        $this->conf = $typoScriptService->convertTypoScriptArrayToPlainArray(
            $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cart.']
        );
        $this->settings = $this->conf['settings'];

        $this->persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager'
        );

        $this->configurationManager = $this->objectManager->get(
            'TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface'
        );

        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                'Cart',
                'Cart'
            );

        $this->cartUtility = $this->objectManager->get(
            'Extcode\\Cart\\Utility\\CartUtility'
        );

        $this->parserUtility = $this->objectManager->get(
            'Extcode\\Cart\\Utility\\ParserUtility'
        );

        return $this;
    }

    public function dispatch()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->parseData();

        $products = $this->cartUtility->getProductsFromRequest(
            $this->pluginSettings,
            $this->request,
            $this->cart->getTaxClasses()
        );

        $quantity = 0;
        foreach ($products as $product) {
            $this->cart->addProduct($product);
            $quantity += $product->getQuantity();
        }

        $this->cartUtility->writeCartToSession($this->cart, $this->settings);

        $response = [
            'status' => '200',
            'added' => $quantity,
            'count' => $this->cart->getCount(),
            'net' => $this->cart->getNet(),
            'gross' => $this->cart->getGross(),
        ];

        return json_encode($response);
    }

    /**
     * Set the request array from the getPost array
     */
    protected function setRequestArgumentsFromGetPost($request)
    {
        $validArguments = array('extensionName', 'pluginName', 'controllerName', 'actionName', 'arguments');
        foreach ($validArguments as $argument) {
            if ($request[$argument]) {
                $this->requestArguments[$argument] = $request[$argument];
            }
        }
    }

    /**
     * Parse Data
     *
     * @return void
     */
    protected function parseData()
    {
        // parse all shippings
        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $this->cart);

        // parse all payments
        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $this->cart);

        // parse all specials
        $this->specials = $this->parserUtility->parseServices('Special', $this->pluginSettings, $this->cart);
    }
}
