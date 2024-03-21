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
use Extcode\Cart\Event\Session\AfterRestoreAddressEvent;
use Extcode\Cart\Event\Session\AfterRestoreCartEvent;
use Extcode\Cart\Event\Session\BeforeWriteAddressEvent;
use Extcode\Cart\Event\Session\BeforeWriteCartEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\SingletonInterface;

class SessionHandler implements SingletonInterface
{
    protected $prefixKey = 'cart_';

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * restore a Cart object from session
     */
    public function restoreCart(string $key): ?Cart
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);

        if (is_string($sessionData)) {
            $cart = unserialize($sessionData);
            if ($cart instanceof Cart) {
                $afterRestoreCartEvent = new AfterRestoreCartEvent($cart);
                $this->eventDispatcher->dispatch($afterRestoreCartEvent);
                return $afterRestoreCartEvent->getCart();
            }
        }

        return null;
    }

    /**
     * writes a Cart object to session
     */
    public function writeCart(string $key, Cart $cart): void
    {
        $beforeWriteCartEvent = new BeforeWriteCartEvent($cart);
        $this->eventDispatcher->dispatch($beforeWriteCartEvent);
        $sessionData = serialize($beforeWriteCartEvent->getCart());

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
                $afterRestoreAddressEvent = new AfterRestoreAddressEvent($address);
                $this->eventDispatcher->dispatch($afterRestoreAddressEvent);
                return $afterRestoreAddressEvent->getAddress();
            }
        }

        return null;
    }

    /**
     * writes an AbstractAddress object to session
     */
    public function writeAddress(string $key, AbstractAddress $address)
    {
        $beforeWriteAddressEvent = new BeforeWriteAddressEvent($address);
        $this->eventDispatcher->dispatch($beforeWriteAddressEvent);
        $sessionData = serialize($beforeWriteAddressEvent->getAddress());

        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
    }
}
