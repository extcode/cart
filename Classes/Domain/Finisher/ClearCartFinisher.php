<?php

namespace Extcode\Cart\Domain\Finisher;

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
 * Clear Cart Finisher
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ClearCartFinisher extends AbstractFinisher
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
