<?php

namespace Extcode\Cart\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Coupon extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var string
     */
    protected $couponType = '';

    /**
     * @var float
     */
    protected $discount = 0.0;

    /**
     * @var int
     */
    protected $taxClassId;

    /**
     * @var float
     */
    protected $cartMinPrice = 0.0;

    /**
     * @var bool
     */
    protected $isCombinable = false;

    /**
     * @var bool
     */
    protected $isRelativeDiscount = false;

    /**
     * @var bool
     */
    protected $handleAvailable = false;

    /**
     * @var int
     */
    protected $numberAvailable = 0;

    /**
     * @var int
     */
    protected $numberUsed = 0;

    /**
     * @param string $title
     * @param string $code
     * @param string $couponType
     * @param float $discount
     * @param int $taxClassId
     */
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

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCouponType(): string
    {
        return $this->couponType;
    }

    /**
     * @return bool
     */
    public function isRelativeDiscount(): bool
    {
        return $this->isRelativeDiscount;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getTaxClassId(): int
    {
        return $this->taxClassId;
    }

    /**
     * @return float
     */
    public function getCartMinPrice(): float
    {
        return $this->cartMinPrice;
    }

    /**
     * @param float $cartMinPrice
     */
    public function setCartMinPrice(float $cartMinPrice)
    {
        $this->cartMinPrice = $cartMinPrice;
    }

    /**
     * @return bool
     */
    public function getHandleAvailable(): bool
    {
        return $this->handleAvailable;
    }

    /**
     * @param bool $handleAvailable
     */
    public function setHandleAvailable(bool $handleAvailable)
    {
        $this->handleAvailable = $handleAvailable;
    }

    /**
     * @return int
     */
    public function getNumberAvailable(): int
    {
        return $this->numberAvailable;
    }

    /**
     * @param int $numberAvailable
     */
    public function setNumberAvailable(int $numberAvailable)
    {
        $this->numberAvailable = $numberAvailable;
    }

    /**
     * @return int
     */
    public function getNumberUsed(): int
    {
        return $this->numberUsed;
    }

    /**
     * Increase the number how often the coupon was used
     */
    public function incNumberUsed()
    {
        $this->numberUsed += 1;
    }

    /**
     * @param int $numberUsed
     */
    public function setNumberUsed(int $numberUsed)
    {
        $this->numberUsed = $numberUsed;
    }

    /**
     * @return bool
     */
    public function getIsCombinable(): bool
    {
        return $this->isCombinable;
    }

    /**
     * @param bool $isCombinable
     */
    public function setIsCombinable(bool $isCombinable)
    {
        $this->isCombinable = $isCombinable;
    }

    /**
     * @return bool
     */
    public function getIsAvailable(): bool
    {
        if ($this->handleAvailable) {
            $available = $this->numberAvailable - $this->numberUsed;

            return $available > 0;
        }

        return true;
    }
}
