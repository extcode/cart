<?php

namespace Extcode\Cart\Domain\Model;

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
 * Cart Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Cart extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * fHash
     *
     * @var string
     */
    protected $fHash;

    /**
     * sHash
     *
     * @var string
     */
    protected $sHash;

    /**
     * FeUser
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser;

    /**
     * Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $orderItem;

    /**
     * Cart
     *
     * @var string
     */
    protected $cart;

    /**
     * Was Ordered
     *
     * @var boolean
     */
    protected $wasOrdered;

    /**
     * Cart constructor
     */
    public function __construct()
    {
        $this->fHash = bin2hex(openssl_random_pseudo_bytes(32));
        $this->sHash = bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * Returns fHash
     *
     * @return string
     */
    public function getFHash()
    {
        return $this->fHash;
    }

    /**
     * Sets fHash
     *
     * @return void
     */
    public function setFHash($fHash)
    {
        $this->fHash = $fHash;
    }

    /**
     * Returns sHash
     *
     * @return string
     */
    public function getSHash()
    {
        return $this->sHash;
    }

    /**
     * Sets sHash
     *
     * @return void
     */
    public function setSHash($sHash)
    {
        $this->sHash = $sHash;
    }

    /**
     * Returns FeUser
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Sets FeUser
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     *
     * @return void
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * Returns Item
     *
     * @return \Extcode\Cart\Domain\Model\Order\Item
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * Sets Item
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     *
     * @return void
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * Returns Cart
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getCart()
    {
        return unserialize($this->cart);
    }

    /**
     * Sets Cart
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @return void
     */
    public function setCart($cart)
    {
        $this->cart = serialize($cart);
    }

    /**
     * Returns Was Ordered
     *
     * @return boolean
     */
    public function getWasOrdered()
    {
        return $this->wasOrdered;
    }

    /**
     * Set Was Ordered
     *
     * @param boolean $wasOrdered
     *
     * @return void
     */
    public function setWasOrdered($wasOrdered)
    {
        $this->wasOrdered = $wasOrdered;
    }
}
