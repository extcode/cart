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
 * Action Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Session Handler
     *
     * @var \Extcode\Cart\Service\SessionHandler
     */
    protected $sessionHandler;

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
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Payments
     *
     * @var array
     */
    protected $payments = [];

    /**
     * Shippings
     *
     * @var array
     */
    protected $shippings = [];

    /**
     * Specials
     *
     * @var array
     */
    protected $specials = [];

    /**
     * @param \Extcode\Cart\Service\SessionHandler $sessionHandler
     */
    public function injectSessionHandler(
        \Extcode\Cart\Service\SessionHandler $sessionHandler
    ) {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param \Extcode\Cart\Utility\CartUtility $cartUtility
     */
    public function injectCartUtility(
        \Extcode\Cart\Utility\CartUtility $cartUtility
    ) {
        $this->cartUtility = $cartUtility;
    }

    /**
     * @param \Extcode\Cart\Utility\ParserUtility $parserUtility
     */
    public function injectParserUtility(
        \Extcode\Cart\Utility\ParserUtility $parserUtility
    ) {
        $this->parserUtility = $parserUtility;
    }

    /**
     * Action initialize
     */
    public function initializeAction()
    {
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }

    protected function updateService()
    {
        $this->parseData();

        if (!$this->cart->getPayment()->isAvailable($this->cart->getGross())) {
            $fallBackId = $this->cart->getPayment()->getFallBackId();
            if ($fallBackId) {
                $payment = $this->cartUtility->getServiceById($this->payments, $fallBackId);
                $this->cart->setPayment($payment);
            }
        }

        if (!$this->cart->getShipping()->isAvailable($this->cart->getGross())) {
            $fallBackId = $this->cart->getShipping()->getFallBackId();
            if ($fallBackId) {
                $shipping = $this->cartUtility->getServiceById($this->shippings, $fallBackId);
                $this->cart->setShipping($shipping);
            }
        }
    }

    /**
     * Parse Data
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

    /**
     *
     */
    protected function restoreSession()
    {
        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        if (!$this->cart instanceof \Extcode\Cart\Domain\Model\Cart\Cart) {
            $this->cart = $this->cartUtility->getNewCart($this->pluginSettings);
            $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);
        }
    }
}
