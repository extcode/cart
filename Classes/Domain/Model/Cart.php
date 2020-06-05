<?php

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

class Cart extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $fHash = '';

    /**
     * @var string
     */
    protected $sHash = '';

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $orderItem = null;

    /**
     * @var string
     */
    protected $cart = null;

    /**
     * @var bool
     */
    protected $wasOrdered = false;

    public function __construct()
    {
        $this->fHash = bin2hex(openssl_random_pseudo_bytes(32));
        $this->sHash = bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * @return string
     */
    public function getFHash(): string
    {
        return $this->fHash;
    }

    /**
     * @return string
     */
    public function getSHash(): string
    {
        return $this->sHash;
    }

    /**
     * @return FrontendUser|null
     */
    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    /**
     * @param FrontendUser $feUser
     */
    public function setFeUser(FrontendUser $feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * @return Item|null
     */
    public function getOrderItem(): ?Item
    {
        return $this->orderItem;
    }

    /**
     * @param Item $orderItem
     */
    public function setOrderItem(Item $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\Cart|null
     */
    public function getCart(): ?\Extcode\Cart\Domain\Model\Cart\Cart
    {
        return unserialize($this->cart);
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function setCart(\Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        $this->cart = serialize($cart);
    }

    /**
     * @return bool
     */
    public function getWasOrdered(): bool
    {
        return $this->wasOrdered;
    }

    /**
     * @param bool $wasOrdered
     */
    public function setWasOrdered(bool $wasOrdered)
    {
        $this->wasOrdered = $wasOrdered;
    }
}
