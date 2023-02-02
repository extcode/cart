<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Cart extends AbstractEntity
{
    protected string $fHash = '';

    protected string $sHash = '';

    protected ?FrontendUser $feUser = null;

    protected ?Item $orderItem = null;

    protected ?string $cart = null;

    protected bool $wasOrdered = false;

    public function __construct()
    {
        $this->fHash = bin2hex(openssl_random_pseudo_bytes(32));
        $this->sHash = bin2hex(openssl_random_pseudo_bytes(32));
    }

    public function getFHash(): string
    {
        return $this->fHash;
    }

    public function getSHash(): string
    {
        return $this->sHash;
    }

    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    public function setFeUser(FrontendUser $feUser): void
    {
        $this->feUser = $feUser;
    }

    public function getOrderItem(): ?Item
    {
        return $this->orderItem;
    }

    public function setOrderItem(Item $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    public function getCart(): ?Cart\Cart
    {
        return unserialize($this->cart);
    }

    public function setCart(Cart\Cart $cart): void
    {
        $this->cart = serialize($cart);
    }

    /**
     * @deprecated
     */
    public function getWasOrdered(): bool
    {
        return $this->wasOrdered();
    }

    public function wasOrdered(): bool
    {
        return $this->wasOrdered;
    }

    public function setWasOrdered(bool $wasOrdered): void
    {
        $this->wasOrdered = $wasOrdered;
    }
}
