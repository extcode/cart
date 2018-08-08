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
 * Coupon Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Coupon extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * Code
     *
     * @var string
     * @validate NotEmpty
     */
    protected $code = '';

    /**
     * Coupon Type
     *
     * @var string
     * @validate NotEmpty
     */
    protected $couponType = '';

    /**
     * Discount
     *
     * @var float
     */
    protected $discount = 0.0;

    /**
     * Tax Class Id
     *
     * @var int
     */
    protected $taxClassId;

    /**
     * Cart Min Price
     *
     * @var float
     */
    protected $cartMinPrice = 0.0;

    /**
     * Is Combinable
     *
     * @var bool
     */
    protected $isCombinable = false;

    /**
     * Is Relative Discount
     *
     * @var bool
     */
    protected $isRelativeDiscount = false;

    /**
     * Handle Available
     *
     * @var bool
     */
    protected $handleAvailable = false;

    /**
     * Number Available
     *
     * @var int
     */
    protected $numberAvailable = 0;

    /**
     * Number Used
     *
     * @var int
     */
    protected $numberUsed = 0;

    /**
     * __construct
     *
     * @param string $title
     * @param string $code
     * @param string $couponType
     * @param float $discount
     * @param int $taxClassId
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $title,
        $code,
        $couponType,
        $discount,
        $taxClassId
    ) {
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1456840910
            );
        }
        if (!$code) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $code for constructor.',
                1456840920
            );
        }
        if (!$couponType) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $couponType for constructor.',
                1468927505
            );
        }
        if (!$discount) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $discount for constructor.',
                1456840930
            );
        }
        if (!$taxClassId) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $taxClassId for constructor.',
                1456840940
            );
        }

        $this->title = $title;
        $this->code = $code;
        $this->couponType = $couponType;
        $this->discount = $discount;
        $this->taxClassId = $taxClassId;
    }

    /**
     * Gets Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets Code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Gets Coupon Type
     *
     * @return string
     */
    public function getCouponType()
    {
        return $this->couponType;
    }

    /**
     * Is Relative Discount
     *
     * @return bool
     */
    public function isRelativeDiscount()
    {
        return $this->isRelativeDiscount;
    }

    /**
     * Gets Discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Gets Tax Class Id
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return $this->taxClassId;
    }

    /**
     * Returns Cart Min Price
     *
     * @return float
     */
    public function getCartMinPrice()
    {
        return $this->cartMinPrice;
    }

    /**
     * Sets Cart Min Price
     *
     * @param float $cartMinPrice
     */
    public function setCartMinPrice($cartMinPrice)
    {
        $this->cartMinPrice = $cartMinPrice;
    }

    /**
     * @return bool
     */
    public function getHandleAvailable()
    {
        return $this->handleAvailable;
    }

    /**
     * @param bool $handleAvailable
     */
    public function setHandleAvailable($handleAvailable)
    {
        $this->handleAvailable = $handleAvailable;
    }

    /**
     * @return int
     */
    public function getNumberAvailable()
    {
        return $this->numberAvailable;
    }

    /**
     * @param int $numberAvailable
     */
    public function setNumberAvailable($numberAvailable)
    {
        $this->numberAvailable = $numberAvailable;
    }

    /**
     * Returns the number how often the coupon was used
     *
     * @return int
     */
    public function getNumberUsed()
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
     * Set the number how often the coupon was used
     *
     * @param int $numberUsed
     */
    public function setNumberUsed($numberUsed)
    {
        $this->numberUsed = $numberUsed;
    }

    /**
     * @return bool
     */
    public function getIsCombinable()
    {
        return $this->isCombinable;
    }

    /**
     * @param bool $isCombinable
     */
    public function setIsCombinable($isCombinable)
    {
        $this->isCombinable = $isCombinable;
    }

    /**
     * Gets Is Available
     *
     * @return bool
     */
    public function getIsAvailable()
    {
        if ($this->handleAvailable) {
            $available = $this->numberAvailable - $this->numberUsed;

            return $available > 0;
        }

        return true;
    }
}
