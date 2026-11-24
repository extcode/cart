<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass as CartTaxClass;
use TYPO3\CMS\Extbase\Attribute\ORM\Lazy;
use TYPO3\CMS\Extbase\Attribute\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

class Discount extends AbstractEntity
{
    #[Lazy]
    protected Item|LazyLoadingProxy $item;

    protected ?TaxClass $taxClass = null;

    /**
     * Class of the cart coupon, e.g. \Extcode\Cart\Domain\Model\Cart\CartCouponPercentage.
     */
    protected string $couponType = '';

    /**
     * JSON encoded taxes of the discount per tax class of the order (uid => tax).
     * An empty string means that the taxes per tax class are unknown (orders
     * which were not migrated by the upgrade wizard).
     *
     * @var string JSON, the tag keeps Extbase from taking the array type of getTaxes() for the property
     */
    protected string $taxes = '';

    /**
     * The discount is not changed after the order was persisted, so there are
     * no setters.
     *
     * @param null|CartTaxClass|TaxClass $taxClass the tax class of the order; passing the tax class of the cart is deprecated
     * @param string $couponType class of the cart coupon
     * @param null|array<int, float> $taxes uid of the tax class of the order => tax, null if unknown
     */
    public function __construct(
        #[Validate(validator: 'NotEmpty')]
        protected string $title,
        #[Validate(validator: 'NotEmpty')]
        protected string $code,
        #[Validate(validator: 'NotEmpty')]
        protected float $gross,
        #[Validate(validator: 'NotEmpty')]
        protected float $net,
        null|CartTaxClass|TaxClass $taxClass,
        #[Validate(validator: 'NotEmpty')]
        protected float $tax,
        string $couponType = '',
        ?array $taxes = null
    ) {
        // TODO: 13.0.0 Accept only the tax class of the order, see Deprecation #896.
        if ($taxClass instanceof CartTaxClass) {
            trigger_error(
                'Passing a tax class of the cart (' . CartTaxClass::class . ') to ' . self::class . ' is deprecated'
                . ' since cart 12.8 and will stop working in cart 13.0. Pass the tax class of the order ('
                . TaxClass::class . ') instead.',
                E_USER_DEPRECATED
            );
            $taxClass = null;
        }

        $this->taxClass = $taxClass;
        $this->couponType = $couponType;
        if ($taxes !== null) {
            $this->taxes = json_encode((object) $taxes, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION);
        }
    }

    public function getItem(): ?Item
    {
        if ($this->item instanceof LazyLoadingProxy) {
            $this->item->_loadRealInstance();
        }

        return $this->item;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * Returns the tax class of the order the coupon was configured with. The
     * tax class is not stored for orders created before cart 12.8.
     */
    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    /**
     * Returns the tax of the coupon in its own tax class. It is 0.0 for
     * percentage coupons, their tax is split over the tax classes of the
     * products, see getTaxes().
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    public function getCouponType(): string
    {
        return $this->couponType;
    }

    /**
     * Returns the taxes of the discount per tax class of the order.
     *
     * @return array<int, float> uid of the tax class of the order => tax
     */
    public function getTaxes(): array
    {
        if ($this->taxes === '') {
            return [];
        }

        $taxes = json_decode($this->taxes, true);
        if (!is_array($taxes)) {
            return [];
        }

        $result = [];
        foreach ($taxes as $taxClassUid => $tax) {
            if (is_numeric($taxClassUid) && is_numeric($tax)) {
                $result[(int) $taxClassUid] = (float) $tax;
            }
        }

        return $result;
    }

    /**
     * Returns false for discounts of orders created before cart 12.8 which
     * were not migrated by the upgrade wizard "extcodeCartOrderDiscountTaxes".
     */
    public function hasTaxes(): bool
    {
        return $this->taxes !== '';
    }
}
