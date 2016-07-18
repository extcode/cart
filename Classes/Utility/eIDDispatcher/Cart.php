<?php

namespace Extcode\Cart\Utility\eIDDispatcher;

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
 * eIDDispatcher Cart
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Cart
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Configuration Manager
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager;

    /**
     * Request
     *
     * @var \TYPO3\CMS\Extbase\Mvc\Request
     */
    protected $request;

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
     * @var array
     */
    protected $conf = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings = [];

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(
        \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
     */
    public function injectPersistenceManager(
        \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager $persistenceManager
    ) {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Initialize Utility Classes
     */
    protected function initUtilities()
    {
        $this->cartUtility = $this->objectManager->get(
            'Extcode\\Cart\\Utility\\CartUtility'
        );

        $this->parserUtility = $this->objectManager->get(
            'Extcode\\Cart\\Utility\\ParserUtility'
        );
    }

    /**
     * Initialize Settings
     */
    protected function initSettings()
    {
        $typoScriptService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Extbase\\Service\\TypoScriptService'
        );

        $this->conf = $typoScriptService->convertTypoScriptArrayToPlainArray(
            $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cart.']
        );

        $this->settings = $this->conf['settings'];

        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
                'Cart',
                'Cart'
            );
    }

    /**
     * Get Request
     */
    protected function getRequest()
    {
        $request = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('request');
        $action = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('eID');

        $this->request = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Request');
        $this->request->setControllerVendorName('Extcode');
        $this->request->setcontrollerExtensionName('Cart');
        $this->request->setPluginName('Cart');
        $this->request->setControllerName('Cart');
        $this->request->setControllerActionName($action);

        $parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::_GPmerged('tx_cart_cart');
        $this->request->setArguments($parameters);
    }

    /**
     * Initialize
     *
     * @return \Extcode\Cart\Utility\eIDDispatcher\Cart
     */
    public function init()
    {
        $this->initUtilities();
        $this->initSettings();

        $this->getRequest();

        return $this;
    }

    /**
     * @return mixed
     */
    public function dispatch()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $response = [];

        switch ($this->request->getControllerActionName()) {
            case 'addProduct':
                $response = $this->addProductAction();
                break;
        }

        return json_encode($response);
    }

    /**
     * Add Product Action
     *
     * @return array
     */
    protected function addProductAction()
    {
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

        $response = $this->changeResponseAfterAjaxAddProduct($response);

        return $response;
    }

    /**
     * Change Response after Ajax Add Product
     *
     * @param array $response
     *
     * @return array
     */
    protected function changeResponseAfterAjaxAddProduct($response = [])
    {
        $data = [
            'cart' => $this->cart,
            'response' => &$response
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            'TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher'
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            'changeResponseAfterAjaxAddProduct',
            [$data]
        );

        return $response;
    }
}
