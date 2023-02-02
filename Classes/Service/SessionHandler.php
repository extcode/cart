<?php

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\AbstractAddress;
use TYPO3\CMS\Core\SingletonInterface;

class SessionHandler implements SingletonInterface
{
    protected $prefixKey = 'cart_';

    /**
     * restore a Cart object from session
     */
    public function restoreCart(string $key): ?Cart
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);

        if (is_string($sessionData)) {
            $cart = unserialize($sessionData);
            if ($cart instanceof Cart) {
                return $cart;
            }
        }

        return null;
    }

    /**
     * writes a Cart object to session
     */
    public function writeCart(string $key, Cart $cart): void
    {
        $sessionData = serialize($cart);

        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * removes a Cart object from session
     */
    public function clearCart(string $key)
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, null);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }

    /**
     * restore an AbstractAddress object from session
     */
    public function restoreAddress(string $key): ?AbstractAddress
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);

        if (is_string($sessionData)) {
            $address = unserialize($sessionData);
            if ($address instanceof AbstractAddress) {
                return $address;
            }
        }

        return null;
    }

    /**
     * writes an AbstractAddress object to session
     */
    public function writeAddress(string $key, AbstractAddress $address)
    {
        $sessionData = serialize($address);

        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }
}
