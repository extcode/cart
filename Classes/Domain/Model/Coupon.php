<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Coupon extends AbstractEntity
{
    protected string $title = '';

    protected string $code = '';

    protected string $couponType = '';

    protected float $discount = 0.0;

    protected int $taxClassId;

    protected float $cartMinPrice = 0.0;

    protected bool $isCombinable = false;

    protected bool $isRelativeDiscount = false;

    protected bool $handleAvailable = false;

    protected int $numberAvailable = 0;

    protected int $numberUsed = 0;

    public function __construct(
        string $title,
        string $code,
        string $couponType,
        float $discount,
        int $taxClassId
    ) {
        $this->title = $title;
        $this->code = $code;
        $this->couponType = $couponType;
        $this->discount = $discount;
        $this->taxClassId = $taxClassId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCouponType(): string
    {
        return $this->couponType;
    }

    public function isRelativeDiscount(): bool
    {
        return $this->isRelativeDiscount;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTaxClassId(): int
    {
        return $this->taxClassId;
    }

    public function getCartMinPrice(): float
    {
        return $this->cartMinPrice;
    }

    public function setCartMinPrice(float $cartMinPrice): void
    {
        $this->cartMinPrice = $cartMinPrice;
    }

    public function isHandleAvailabilityEnabled(): bool
    {
        return $this->handleAvailable;
    }

    public function setIsHandleAvailabilityEnabled(bool $handleAvailable): void
    {
        $this->handleAvailable = $handleAvailable;
    }

    public function getNumberAvailable(): int
    {
        return $this->numberAvailable;
    }

    public function setNumberAvailable(int $numberAvailable): void
    {
        $this->numberAvailable = $numberAvailable;
    }

    public function getNumberUsed(): int
    {
        return $this->numberUsed;
    }

    /**
     * Increase the number how often the coupon was used
     */
    public function incNumberUsed(): void
    {
        $this->numberUsed += 1;
    }

    public function setNumberUsed(int $numberUsed): void
    {
        $this->numberUsed = $numberUsed;
    }

    public function isCombinable(): bool
    {
        return $this->isCombinable;
    }

    public function setIsCombinable(bool $isCombinable): void
    {
        $this->isCombinable = $isCombinable;
    }

    public function isAvailable(): bool
    {
        if ($this->handleAvailable) {
            $available = $this->numberAvailable - $this->numberUsed;

            return $available > 0;
        }

        return true;
    }
}
