<?php
declare(strict_types=1);
namespace Extcode\Cart\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Utility\CartUtility;
use Extcode\Cart\Utility\ParserUtility;

class ClearCart
{
    /**
     * @var CartUtility
     */
    protected $cartUtility;

    /**
     * @var ParserUtility
     */
    protected $parserUtility;

    /**
     * @var SessionHandler
     */
    protected $sessionHandler;

    public function __construct(
        CartUtility $cartUtility,
        ParserUtility $parserUtility,
        SessionHandler $sessionHandler
    ) {
        $this->cartUtility = $cartUtility;
        $this->parserUtility = $parserUtility;
        $this->sessionHandler = $sessionHandler;
    }

    public function __invoke(EventInterface $event): void
    {
        $cart = $event->getCart();
        $settings = $event->getSettings();

        $paymentId = $cart->getPayment()->getId();
        $paymentSettings = $this->parserUtility->getTypePluginSettings($settings, $cart, 'payments');

        if ((int)($paymentSettings['options'][$paymentId]['preventClearCart'] ?? 0) !== 1) {
            $cart = $this->cartUtility->getNewCart($settings);
        }

        $this->sessionHandler->write($cart, $settings['settings']['cart']['pid']);

        $GLOBALS['TSFE']->fe_user->setKey('ses', 'cart_billing_address_' . $settings['settings']['cart']['pid'], null);
        $GLOBALS['TSFE']->fe_user->setKey('ses', 'cart_shipping_address_' . $settings['settings']['cart']['pid'], null);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }
}
