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
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class SessionHandler implements SingletonInterface
{
    protected $prefixKey = 'cart_';
    private FrontendUserAuthentication $feUser;

    public function __construct()
    {
        $this->getSession();
    }

    /**
     * restore a Cart object from session
     */
    public function restoreCart(string $key): ?Cart
    {
        $sessionData = $this->feUser->getKey('ses', $this->prefixKey . $key);

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

        $this->feUser->setKey('ses', $this->prefixKey . $key, $sessionData);
        $this->feUser->storeSessionData();
    }

    /**
     * removes a Cart object from session
     */
    public function clearCart(string $key): void
    {
        $this->feUser->setKey('ses', $this->prefixKey . $key, null);
        $this->feUser->storeSessionData();
    }

    /**
     * restore an AbstractAddress object from session
     */
    public function restoreAddress(string $key): ?AbstractAddress
    {
        $sessionData = $this->feUser->getKey('ses', $this->prefixKey . $key);

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
    public function writeAddress(string $key, AbstractAddress $address): void
    {
        $sessionData = serialize($address);

        $this->feUser->setKey('ses', $this->prefixKey . $key, $sessionData);
        $this->feUser->storeSessionData();
    }

    private function getSession(): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'];
        $this->feUser = $request->getAttribute('frontend.user');
    }
}
