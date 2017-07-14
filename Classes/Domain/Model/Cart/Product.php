<?php

namespace Extcode\Cart\Domain\Model\Cart;

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
 * Cart Product Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Product
{
    /**
     * Product Type
     *
     * @var string
     */
    protected $productType;

    /**
     * Product Id
     *
     * @var int
     */
    protected $productId;

    /**
     * Table Id
     *
     * @var int
     */
    protected $tableId;

    /**
     * Content Id
     *
     * @var int
     */
    protected $contentId;

    /**
     * Cart
     *
     * @var Cart
     */
    protected $cart = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * SKU
     *
     * @var string
     */
    protected $sku;

    /**
     * Price
     *
     * @var float
     */
    protected $price;

    /**
     * Special Price
     *
     * @var float
     */
    protected $specialPrice = null;

    /**
     * Quantity Discounts
     *
     * @var array
     */
    protected $quantityDiscounts = [];

    /**
     * Quantity
     *
     * @var int
     */
    protected $quantity;

    /**
     * Gross
     *
     * @var float
     */
    protected $gross;

    /**
     * Net
     *
     * @var float
     */
    protected $net;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax;

    /**
     * Error
     *
     * @var string
     */
    protected $error;

    /**
     * Is Virtual Product
     *
     * @var bool
     */
    protected $isVirtualProduct = false;

    /**
     * Service Attribute 1
     *
     * @var float
     */
    protected $serviceAttribute1;

    /**
     * Service Attribute 2
     *
     * @var float
     */
    protected $serviceAttribute2;

    /**
     * Service Attribute 3
     *
     * @var float
     */
    protected $serviceAttribute3;

    /**
     * Is Net Price
     *
     * @var bool
     */
    protected $isNetPrice;

    /**
     * Variants
     *
     * @var array BeVariant
     */
    protected $beVariants;

    /**
     * Frontend Variant
     *
     * @var \Extcode\Cart\Domain\Model\Cart\FeVariant
     */
    protected $feVariant;

    /**
     * Additional
     *
     * @var array Additional
     */
    protected $additional = [];

    /**
     * Min Number In Cart
     *
     * @var int
     */
    protected $minNumberInCart = 0;

    /**
     * Max Number in Cart
     *
     * @var int
     */
    protected $maxNumberInCart = 0;

    /**
     * __construct
     *
     * @param string $productType
     * @param int $productId
     * @param int $tableId
     * @param int $contentId
     * @param string $sku
     * @param string $title
     * @param float $price
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     * @param int $quantity
     * @param bool $isNetPrice
     * @param \Extcode\Cart\Domain\Model\Cart\FeVariant $feVariant
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $productType,
        $productId,
        $tableId,
        $contentId,
        $sku,
        $title,
        $price,
        $taxClass,
        $quantity,
        $isNetPrice = false,
        $feVariant = null
    ) {
        if (!$productType) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $productType for constructor.',
                1468754400
            );
        }
        if (!$productId) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $productId for constructor.',
                1413999100
            );
        }
        if (!$sku) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $sku for constructor.',
                1413999110
            );
        }
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1413999120
            );
        }
        if ($price === null) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $price for constructor.',
                1413999130
            );
        }
        if (!$taxClass) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $taxClass for constructor.',
                1413999140
            );
        }
        if (!$quantity) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $quantity for constructor.',
                1413999150
            );
        }

        $this->productType = $productType;
        $this->productId = $productId;
        $this->tableId = $tableId != null ? $tableId : 0;
        $this->contentId = $contentId;
        $this->sku = $sku;
        $this->title = $title;
        $this->price = $price;
        $this->taxClass = $taxClass;
        $this->quantity = $quantity;
        $this->isNetPrice = $isNetPrice;

        if ($feVariant) {
            $this->feVariant = $feVariant;
        }

        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @param $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function getIsNetPrice()
    {
        return $this->isNetPrice;
    }

    /**
     * @param array $newVariants
     * @return mixed
     */
    public function addBeVariants($newVariants)
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $newVariant
     * @return mixed
     */
    public function addBeVariant(\Extcode\Cart\Domain\Model\Cart\BeVariant $newVariant)
    {
        $newVariantsId = $newVariant->getId();
        /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
        $variant = $this->beVariants[$newVariantsId];

        if ($variant) {
            if ($variant->getBeVariants()) {
                $variant->addBeVariants($newVariant->getBeVariants());
            } else {
                $newQuantity = $variant->getQuantity() + $newVariant->getQuantity();
                $variant->setQuantity($newQuantity);
            }
        } else {
            $this->beVariants[$newVariantsId] = $newVariant;
        }

        $this->reCalc();
    }

    /**
     * @param array $variantQuantity
     */
    public function changeVariantsQuantity($variantQuantity)
    {
        foreach ($variantQuantity as $variantId => $quantity) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
            $variant = $this->beVariants[$variantId];

            if (ctype_digit($quantity)) {
                $quantity = intval($quantity);
                $variant->changeQuantity($quantity);
            } elseif (is_array($quantity)) {
                $variant->changeVariantsQuantity($quantity);
            }

            $this->reCalc();
        }
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\FeVariant
     */
    public function getFeVariant()
    {
        return $this->feVariant;
    }

    /**
     * @return array
     */
    public function getBeVariants()
    {
        return $this->beVariants;
    }

    /**
     * @param $variantId
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getBeVariantById($variantId)
    {
        return $this->beVariants[$variantId];
    }

    /**
     * @param $variantId
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getBeVariant($variantId)
    {
        return $this->getBeVariantById($variantId);
    }

    /**
     * @param $variantsArray
     * @return bool|int
     */
    public function removeBeVariants($variantsArray)
    {
        foreach ($variantsArray as $variantId => $value) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
            $variant = $this->beVariants[$variantId];
            if ($variant) {
                if (is_array($value)) {
                    $variant->removeBeVariants($value);

                    if (!$variant->getBeVariants()) {
                        unset($this->beVariants[$variantId]);
                    }

                    $this->reCalc();
                } else {
                    unset($this->beVariants[$variantId]);

                    $this->reCalc();
                }
            } else {
                return -1;
            }
        }

        return true;
    }

    /**
     * @param $variantId
     * @return array
     */
    public function removeVariantById($variantId)
    {
        unset($this->beVariants[$variantId]);

        $this->reCalc();
    }

    /**
     * @param $variantId
     * @return array
     */
    public function removeVariant($variantId)
    {
        $this->removeVariantById($variantId);
    }

    /**
     * @param $variantId
     * @param $newQuantity
     * @internal param $id
     */
    public function changeVariantById($variantId, $newQuantity)
    {
        $this->beVariants[$variantId]->changeQuantity($newQuantity);

        $this->reCalc();
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getTableId()
    {
        return $this->tableId;
    }

    /**
     * @return int
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        if (!$this->contentId) {
            $id = $this->getTableProductId();
        } else {
            $id = $this->getContentProductId();
        }
        return $id;
    }

    /**
     * @return string
     */
    protected function getTableProductId()
    {
        $tableProductId = $this->getTableId() . '_' . $this->getProductId();
        if ($this->getFeVariant()) {
            $tableProductId .= '_' . $this->getFeVariant()->getId();
        }
        return 't_' . $tableProductId;
    }

    /**
     * @return string
     */
    protected function getContentProductId()
    {
        return 'c_' . $this->getContentId() . '_' . $this->getProductId();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getTranslatedPrice()
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getPrice());
        }

        return $this->getPrice();
    }

    /**
     * Returns Special Price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @return float
     */
    public function getTranslatedSpecialPrice()
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getSpecialPrice());
        }

        return $this->getSpecialPrice();
    }

    /**
     * Sets Special Price
     *
     * @param float $specialPrice
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * Returns Quantity Discounts
     *
     * @return array
     */
    public function getQuantityDiscounts()
    {
        return $this->quantityDiscounts;
    }

    /**
     * Returns Quantity Discount Price
     *
     * @return float
     */
    public function getQuantityDiscountPrice()
    {
        $price = $this->getTranslatedPrice();

        if ($this->getQuantityDiscounts()) {
            foreach ($this->getQuantityDiscounts() as $quantityDiscount) {
                if (($quantityDiscount['quantity'] <= $this->getQuantity()) && ($quantityDiscount['price'] < $price)) {
                    $price = $quantityDiscount['price'];
                }
            }
        }

        return $price;
    }

    /**
     * Set Quantity Discounts
     *
     * @param array $quantityDiscounts
     */
    public function setQuantityDiscounts($quantityDiscounts)
    {
        $this->quantityDiscounts = $quantityDiscounts;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     *
     * @return float
     */
    public function getBestPrice()
    {
        $bestPrice = $this->getQuantityDiscountPrice();

        if ($this->getTranslatedSpecialPrice() && ($this->getTranslatedSpecialPrice() < $bestPrice)) {
            $bestPrice = $this->getTranslatedSpecialPrice();
        }

        return $bestPrice;
    }

    /**
     * Returns Best Price Discount
     *
     * @return float
     */
    public function getDiscount()
    {
        $discount = $this->getTranslatedPrice() - $this->getBestPrice();

        return $discount;
    }

    /**
     * Returns Special Price
     *
     * @return float
     */
    public function getSpecialPriceDiscount()
    {
        $discount = 0.0;
        if (($this->getTranslatedPrice() != 0.0) && ($this->getTranslatedSpecialPrice())) {
            $discount = (($this->getTranslatedPrice() - $this->getTranslatedSpecialPrice()) / $this->getTranslatedPrice()) * 100;
        }
        return $discount;
    }

    /**
     * @return float
     */
    public function getPriceTax()
    {
        return ($this->getBestPrice() / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
    }

    /**
     * Returns TaxClass
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Sets TaxClass
     *
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     */
    public function setTaxClass(\Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * @param int $newQuantity
     */
    public function changeQuantity($newQuantity)
    {
        if ($this->quantity != $newQuantity) {
            if ($newQuantity === 0) {
                $this->cart->removeproduct($this);
            } else {
                $this->quantity = $newQuantity;
                $this->reCalc();
            }
        }
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Quantity Leaving Range
     *
     * returns 0 if quantity is in rage
     * returns -1 if minimum was not reached
     * returns 1 if maximum was exceeded
     *
     * @return int
     */
    protected function isQuantityLeavingRange()
    {
        $outOfRange = 0;

        if ($this->getQuantityBelowRange()) {
            $outOfRange = -1;
        }
        if ($this->getQuantityAboveRange()) {
            $outOfRange = 1;
        }

        return $outOfRange;
    }

    /**
     * Quantity In Range
     *
     * @return bool
     */
    protected function isQuantityInRange()
    {
        $inRange = true;
        if ($this->isQuantityLeavingRange() != 0) {
            $inRange = false;
        }
        return $inRange;
    }

    /**
     * @return bool
     */
    public function getQuantityIsInRange()
    {
        return $this->isQuantityInRange();
    }

    /**
     * @return int
     */
    public function getQuantityIsLeavingRange()
    {
        return $this->isQuantityLeavingRange();
    }

    /**
     * Returns true if Quantity in cart is below Min Number In Cart
     * @return bool
     */
    public function getQuantityBelowRange()
    {
        $belowRange = false;

        if ($this->quantity < $this->minNumberInCart) {
            $belowRange = true;
        }

        return $belowRange;
    }

    /**
     * Returns true if Quantity in cart is above Max Number In Cart
     *
     * @return bool
     */
    public function getQuantityAboveRange()
    {
        $aboveRange = false;

        if (($this->maxNumberInCart > 0) && ($this->quantity > $this->maxNumberInCart)) {
            $aboveRange = true;
        }

        return $aboveRange;
    }

    /**
     * @return float
     */
    public function getGross()
    {
        $gross = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcGross();
            $gross = $this->gross;
        }
        return $gross;
    }

    /**
     * @return float
     */
    public function getNet()
    {
        $net = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcNet();
            $net = $this->net;
        }
        return $net;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        $tax = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcTax();
            $tax = $this->tax;
        }
        return $tax;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function getIsVirtualProduct()
    {
        return $this->isVirtualProduct;
    }

    /**
     * @param bool $isVirtualProduct
     */
    public function setIsVirtualProduct($isVirtualProduct)
    {
        $this->isVirtualProduct = $isVirtualProduct;
    }

    /**
     * @return float
     */
    public function getServiceAttribute1()
    {
        return $this->serviceAttribute1;
    }

    /**
     * @param float $serviceAttribute1
     */
    public function setServiceAttribute1($serviceAttribute1)
    {
        $this->serviceAttribute1 = floatval($serviceAttribute1);
    }

    /**
     * @return float
     */
    public function getServiceAttribute2()
    {
        return $this->serviceAttribute2;
    }

    /**
     * @param float $serviceAttribute2
     */
    public function setServiceAttribute2($serviceAttribute2)
    {
        $this->serviceAttribute2 = floatval($serviceAttribute2);
    }

    /**
     * @return float
     */
    public function getServiceAttribute3()
    {
        return $this->serviceAttribute3;
    }

    /**
     * @param float $serviceAttribute3
     */
    public function setServiceAttribute3($serviceAttribute3)
    {
        $this->serviceAttribute3 = floatval($serviceAttribute3);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $productArr = [
            'productId' => $this->productId,
            'tableId' => $this->tableId,
            'contentId' => $this->contentId,
            'id' => $this->getId(),
            'sku' => $this->sku,
            'title' => $this->title,
            'price' => $this->price,
            'specialPrice' => $this->specialPrice,
            'quantityDiscounts' => $this->quantityDiscounts,
            'taxClass' => $this->taxClass,
            'quantity' => $this->quantity,
            'price_total' => $this->gross,
            'price_total_gross' => $this->gross,
            'price_total_net' => $this->net,
            'tax' => $this->tax,
            'additional' => $this->additional
        ];

        if ($this->beVariants) {
            $variantArr = [];

            foreach ($this->beVariants as $variant) {
                /** @var $variant \Extcode\Cart\Domain\Model\Cart\BeVariant */
                array_push($variantArr, [$variant->getId() => $variant->toArray()]);
            }

            array_push($productArr, ['variants' => $variantArr]);
        }

        return $productArr;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        json_encode($this->toArray());
    }

    /**
     */
    protected function calcGross()
    {
        if ($this->isNetPrice == false) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                    $sum += $variant->getGross();
                }
                $this->gross = $sum;
            } else {
                $this->gross = $this->getBestPrice() * $this->quantity;
            }
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->gross = $this->net + $this->tax;
        }
    }

    /**
     */
    protected function calcTax()
    {
        if ($this->isNetPrice == false) {
            $this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
        } else {
            $this->tax = ($this->net * $this->taxClass->getCalc());
        }
    }

    /**
     */
    protected function calcNet()
    {
        if ($this->isNetPrice == true) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                    $sum += $variant->getNet();
                }
                $this->net = $sum;
            } else {
                $this->net = $this->getBestPrice() * $this->quantity;
            }
        } else {
            $this->calcGross();
            $this->calcTax();
            $this->net = $this->gross - $this->tax;
        }
    }

    /**
     */
    protected function reCalc()
    {
        if ($this->beVariants) {
            $quantity = 0;
            foreach ($this->beVariants as $variant) {
                /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                $quantity += $variant->getQuantity();
            }

            if ($this->quantity != $quantity) {
                $this->quantity = $quantity;
            }
        }

        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @return array
     */
    public function getAdditionalArray()
    {
        return $this->additional;
    }

    /**
     * @param $additional
     */
    public function setAdditionalArray($additional)
    {
        $this->additional = $additional;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAdditional($key)
    {
        return $this->additional[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAdditional($key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @return int
     */
    public function getMinNumberInCart()
    {
        return $this->minNumberInCart;
    }

    /**
     * @param int $minNumberInCart
     */
    public function setMinNumberInCart($minNumberInCart)
    {
        if ($minNumberInCart < 0 ||
            ($this->maxNumberInCart > 0 && $minNumberInCart > $this->maxNumberInCart)
        ) {
            throw new \InvalidArgumentException;
        }

        $this->minNumberInCart = $minNumberInCart;
    }

    /**
     * @return int
     */
    public function getMaxNumberInCart()
    {
        return $this->maxNumberInCart;
    }

    /**
     * @param int $maxNumberInCart
     */
    public function setMaxNumberInCart($maxNumberInCart)
    {
        if ($maxNumberInCart < 0 || $maxNumberInCart < $this->minNumberInCart) {
            throw new \InvalidArgumentException;
        }

        $this->maxNumberInCart = $maxNumberInCart;
    }
}
