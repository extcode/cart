<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class ClearCartFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
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

    public function executeInternal()
    {
        $cart = $this->finisherContext->getCart();

        $paymentId = $cart->getPayment()->getId();
        $paymentSettings = $this->parserUtility->getTypePluginSettings($this->settings, $cart, 'payments');

        if (intval($paymentSettings['options'][$paymentId]['preventClearCart']) != 1) {
            $cart = $this->cartUtility->getNewCart($this->settings);
        }

        $this->sessionHandler->write($cart, $this->settings['settings']['cart']['pid']);
    }
}
