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
 * Cart Cart Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Cart
{

    /**
     * Tax Classes
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass[]
     */
    protected $taxClasses;

    /**
     * Net
     *
     * @var float
     */
    protected $net;

    /**
     * Gross
     *
     * @var float
     */
    protected $gross;

    /**
     * Taxes
     *
     * @var array
     */
    protected $taxes;

    /**
     * Count
     *
     * @var int
     */
    protected $count;

    /**
     * Products
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Product[]
     */
    protected $products;

    /**
     * Shipping
     *
     * @var ServiceInterface
     */
    protected $shipping;

    /**
     * Payment
     *
     * @var ServiceInterface
     */
    protected $payment;

    /**
     * Specials
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Special[]
     */
    protected $specials;

    /**
     * Max Service Attribute 1
     *
     * @var float
     */
    protected $maxServiceAttr1 = 0.0;

    /**
     * Max Service Attribute 2
     *
     * @var float
     */
    protected $maxServiceAttr2 = 0.0;

    /**
     * Max Service Attribute 3
     *
     * @var float
     */
    protected $maxServiceAttr3 = 0.0;

    /**
     * Sum Service Attribute 1
     *
     * @var float
     */
    protected $sumServiceAttr1 = 0.0;

    /**
     * Sum Service Attribute 2
     *
     * @var float
     */
    protected $sumServiceAttr2 = 0.0;

    /**
     * Sum Service Attribute 3
     *
     * @var float
     */
    protected $sumServiceAttr3 = 0.0;

    /**
     * Is Net Cart
     *
     * @var bool
     */
    protected $isNetCart;

    /**
     * Order Number
     *
     * @var string
     */
    protected $orderNumber;

    /**
     * Invoice Number
     *
     * @var string
     */
    protected $invoiceNumber;

    /**
     * Additional
     *
     * @var array
     */
    protected $additional = [];

    /**
     * Order Id
     *
     * @var int
     */
    protected $orderId;

    /**
     * Coupon
     *
     * @var \Extcode\Cart\Domain\Model\Cart\CartCouponInterface[]
     */
    protected $coupons = [];

    /**
     * Billing Country
     *
     * @var string
     */
    protected $billingCountry;

    /**
     * Shipping Same As Billing
     *
     * @var bool
     */
    protected $shippingSameAsBilling = true;

    /**
     * Shipping Country
     *
     * @var string
     */
    protected $shippingCountry;

    /**
     * Currency Code
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Currency Sign
     *
     * @var string
     */
    protected $currencySign;

    /**
     * Currency Translation
     *
     * @var string
     */
    protected $currencyTranslation;

    /**
     * __construct
     *
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass[] $taxClasses
     * @param bool $isNetCart
     * @param string $currencyCode
     * @param string $currencySign
     * @param float $currencyTranslation
     */
    public function __construct(
        array $taxClasses,
        $isNetCart = false,
        $currencyCode = 'EUR',
        $currencySign = '€',
        $currencyTranslation = 1.00
    ) {
        $this->taxClasses = $taxClasses;
        $this->net = 0.0;
        $this->gross = 0.0;
        $this->count = 0;
        $this->products = [];

        $this->maxServiceAttr1 = 0.0;
        $this->maxServiceAttr2 = 0.0;
        $this->maxServiceAttr3 = 0.0;
        $this->sumServiceAttr1 = 0.0;
        $this->sumServiceAttr2 = 0.0;
        $this->sumServiceAttr3 = 0.0;

        $this->isNetCart = $isNetCart;

        $this->currencyCode = $currencyCode;
        $this->currencySign = $currencySign;
        $this->currencyTranslation = $currencyTranslation;
    }

    /**
     * __sleep
     *
     * @return array
     */
    public function __sleep()
    {
        return [
            'taxClasses',
            'currencyCode',
            'currencySign',
            'currencyTranslation',
            'net',
            'gross',
            'taxes',
            'count',
            'shipping',
            'payment',
            'specials',
            'service',
            'products',
            'coupons',
            'maxServiceAttr1',
            'maxServiceAttr2',
            'maxServiceAttr3',
            'sumServiceAttr1',
            'sumServiceAttr2',
            'sumServiceAttr3',
            'isNetCart',
            'orderId',
            'orderNumber',
            'invoiceNumber',
            'additional',
            'billingCountry',
            'shippingSameAsBilling',
            'shippingCountry',
        ];
    }

    /**
     * __wakeup
     */
    public function __wakeup()
    {
    }

    /**
     * Gets the Tax Classes Array
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass[]
     */
    public function getTaxClasses()
    {
        return $this->taxClasses;
    }

    /**
     * Sets the Tax Classes Array
     *
     * @param array $taxClasses
     */
    public function setTaxClasses($taxClasses)
    {
        $this->taxClasses = $taxClasses;

        if ($this->products) {
            foreach ($this->products as $product) {
                $taxClassId = $product->getTaxClass()->getId();
                $product->setTaxClass($taxClasses[$taxClassId]);
            }
        }
    }

    /**
     * Gets the Tax Class by Tax Class Id
     *
     * @param int $taxClassId Tax Class Id
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass($taxClassId)
    {
        return $this->taxClasses[$taxClassId];
    }

    /**
     * Sets Is Net Cart
     *
     * @param bool
     */
    public function setIsNetCart($isNetCart)
    {
        $this->isNetCart = $isNetCart;
    }

    /**
     * Gets Is Net Cart
     *
     * @return bool
     */
    public function getIsNetCart()
    {
        return $this->isNetCart;
    }

    /**
     * Sets Order Number if no Order Number is given else throws an exception
     *
     * @param string $orderNumber
     *
     * @throws \LogicException
     */
    public function setOrderNumber($orderNumber)
    {
        if (($this->orderNumber) && ($this->orderNumber != $orderNumber)) {
            throw new \LogicException(
                'You can not redeclare the order number of your cart.',
                1413969668
            );
        }

        $this->orderNumber = $orderNumber;
    }

    /**
     * Gets Order Number
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Sets Invoice Number if no Invoice Number is given else throws an exception
     *
     * @param string $invoiceNumber
     *
     * @throws \LogicException
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        if (($this->invoiceNumber) && ($this->invoiceNumber != $invoiceNumber)) {
            throw new \LogicException(
                'You can not redeclare the invoice number of your cart.',
                1413969712
            );
        }

        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * Gets Invoice Number
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param $net
     */
    public function addNet($net)
    {
        $this->net += $net;
    }

    /**
     * @return float
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * @param $net
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * @param $net
     */
    public function subNet($net)
    {
        $this->net -= $net;
    }

    /**
     * @param $gross
     */
    public function addGross($gross)
    {
        $this->gross += $gross;
    }

    /**
     * @return float
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * @param $gross
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    /**
     * @param $gross
     */
    public function subGross($gross)
    {
        $this->gross -= $gross;
    }

    /**
     * @param float $tax
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     */
    public function addTax($tax, $taxClass)
    {
        $this->taxes[$taxClass->getId()] += $tax;
    }

    /**
     * @return array
     */
    public function getTaxes()
    {
        return $this->taxes;
    }

    /**
     * @return array
     */
    public function getServiceTaxes()
    {
        $taxes = [];

        if ($this->payment) {
            $tax = $this->payment->getTax();
            $taxes[$this->payment->getTaxClass()->getId()] += $tax;
        }
        if ($this->shipping) {
            $tax = $this->shipping->getTax();
            $taxes[$this->shipping->getTaxClass()->getId()] += $tax;
        }
        if ($this->specials) {
            foreach ($this->specials as $special) {
                $tax = $special->getTax();
                $taxes[$special->getTaxClass()->getId()] += $tax;
            }
        }

        return $taxes;
    }

    /**
     * @return array
     */
    public function getSubtotalTaxes()
    {
        $taxes = $this->taxes;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->getIsUseable()) {
                    $tax = $coupon->getTax();
                    $taxes[$coupon->getTaxClass()->getId()] -= $tax;
                }
            }
        }

        return $taxes;
    }

    /**
     * @return array
     */
    public function getTotalTaxes()
    {
        $taxes = $this->getSubtotalTaxes();

        if ($this->payment) {
            $tax = $this->payment->getTax();
            $taxes[$this->payment->getTaxClass()->getId()] += $tax;
        }
        if ($this->shipping) {
            $tax = $this->shipping->getTax();
            $taxes[$this->shipping->getTaxClass()->getId()] += $tax;
        }

        if ($this->specials) {
            foreach ($this->specials as $special) {
                $tax = $special->getTax();
                $taxes[$special->getTaxClass()->getId()] += $tax;
            }
        }

        return $taxes;
    }

    /**
     * @param int $taxClassId
     * @param float $tax
     */
    public function setTax($taxClassId, $tax)
    {
        $this->taxes[$taxClassId] = $tax;
    }

    /**
     * @param float $tax
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     */
    public function subTax($tax, $taxClass)
    {
        $this->taxes[$taxClass->getId()] -= $tax;
    }

    /**
     * @param $count
     */
    public function addCount($count)
    {
        $this->count += $count;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @param $count
     */
    public function subCount($count)
    {
        $this->count -= $count;
    }

    /**
     * @return int
     */
    public function getCountPhysicalProducts()
    {
        $count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                if (!$product->getIsVirtualProduct()) {
                    $count += $product->getQuantity();
                }
            }
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getCountVirtualProducts()
    {
        $count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                if ($product->getIsVirtualProduct()) {
                    $count += $product->getQuantity();
                }
            }
        }

        return $count;
    }

    /**
     * @return ServiceInterface
     */
    public function getShipping(): ServiceInterface
    {
        return $this->shipping;
    }

    /**
     * @param ServiceInterface $shipping
     */
    public function setShipping(ServiceInterface $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return ServiceInterface
     */
    public function getPayment(): ServiceInterface
    {
        return $this->payment;
    }

    /**
     * @param ServiceInterface $payment
     */
    public function setPayment(ServiceInterface $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Special[]
     */
    public function getSpecials()
    {
        return $this->specials;
    }

    /**
     * @param Special $newSpecial
     */
    public function addSpecial($newSpecial)
    {
        $this->specials[$newSpecial->getId()] = $newSpecial;
    }

    /**
     * @param Special $special
     */
    public function removeSpecial($special)
    {
        unset($this->specials[$special->getId()]);
    }

    /**
     * @return float
     */
    public function getServiceNet()
    {
        $net = 0.0;

        if ($this->payment) {
            $net += $this->payment->getNet();
        }
        if ($this->shipping) {
            $net += $this->shipping->getNet();
        }
        if ($this->specials) {
            foreach ($this->specials as $special) {
                $net += $special->getNet();
            }
        }

        return $net;
    }

    /**
     * @return float
     */
    public function getServiceGross()
    {
        $gross = 0.0;

        if ($this->payment) {
            $gross += $this->payment->getGross();
        }
        if ($this->shipping) {
            $gross += $this->shipping->getGross();
        }
        if ($this->specials) {
            foreach ($this->specials as $special) {
                $gross += $special->getGross();
            }
        }

        return $gross;
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $id
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getProductById($id)
    {
        return $this->products[$id];
    }

    /**
     * @param $id
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     *
     * @see getProductById
     */
    public function getProduct($id)
    {
        return $this->getProductById($id);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $cartArray = [
            'net' => $this->net,
            'gross' => $this->gross,
            'count' => $this->count,
            'taxes' => $this->taxes,
            'maxServiceAttribute1' => $this->maxServiceAttr1,
            'maxServiceAttribute2' => $this->maxServiceAttr2,
            'maxServiceAttribute3' => $this->maxServiceAttr3,
            'sumServiceAttribute1' => $this->sumServiceAttr1,
            'sumServiceAttribute2' => $this->sumServiceAttr2,
            'sumServiceAttribute3' => $this->sumServiceAttr3,
            'additional' => $this->additional
        ];

        if ($this->payment) {
            $cartArray['payment'] = $this->payment->getName();
        }

        if ($this->shipping) {
            $cartArray['shipping'] = $this->shipping->getName();
        }

        if ($this->specials) {
            $specials = [];
            foreach ($this->specials as $special) {
                $specials[] = $special->getName();
            }
            $cartArray['specials'] = $specials;
        }

        return $cartArray;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Return Coupons
     *
     * @return array
     */
    public function getCoupons()
    {
        return $this->coupons;
    }

    /**
     * @return bool
     */
    protected function areCouponsCombinable()
    {
        $areCombinable = true;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if (!$coupon->getIsCombinable()) {
                    $areCombinable = false;
                    break;
                }
            }
        }

        return $areCombinable;
    }

    /**
     * Adds a Coupon to Cart
     *
     * @param \Extcode\Cart\Domain\Model\Cart\CartCouponInterface $coupon
     *
     * @return int
     */
    public function addCoupon(\Extcode\Cart\Domain\Model\Cart\CartCouponInterface $coupon)
    {
        if ($this->coupons[$coupon->getCode()]) {
            $returnCode = -1;
        } else {
            if ((!empty($this->coupons)) && (!$this->areCouponsCombinable() || !$coupon->getIsCombinable())) {
                $returnCode = -2;
            } else {
                $coupon->setCart($this);
                $this->coupons[$coupon->getCode()] = $coupon;

                $returnCode = 1;
            }
        }

        return $returnCode;
    }

    /**
     * Remove Coupon with a given Coupon Code from Cart
     *
     * @param string $couponCode
     * @return int
     */
    public function removeCoupon($couponCode)
    {
        if (!$this->coupons[$couponCode]) {
            $returnCode = -1;
        } else {
            unset($this->coupons[$couponCode]);

            $returnCode = 1;
        }

        return $returnCode;
    }

    /**
     * Returns Coupon Gross
     *
     * @return float
     */
    public function getCouponGross()
    {
        $gross = 0.0;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->getIsUseable()) {
                    $gross += $coupon->getGross();
                }
            }
        }

        return $gross;
    }

    /**
     * Returns Coupon Net
     *
     * @return float
     */
    public function getCouponNet()
    {
        $net = 0.0;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->getIsUseable()) {
                    $net += $coupon->getNet();
                }
            }
        }

        return $net;
    }

    /**
     * Returns Coupon Taxes
     *
     * @return array
     */
    public function getCouponTaxes()
    {
        $taxes = [];

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->getIsUseable()) {
                    $tax = $coupon->getTax();
                    $taxes[$coupon->getTaxClass()->getId()] += $tax;
                }
            }
        }

        return $taxes;
    }

    /**
     * Returns Discount Gross of Coupon Gross for Views
     *
     * @return float
     */
    public function getDiscountGross()
    {
        $gross = -1 * $this->getCouponGross();

        return $gross;
    }

    /**
     * Returns Discount Net of Coupon Net for Views
     *
     * @return float
     */
    public function getDiscountNet()
    {
        $net = -1 * $this->getCouponNet();

        return $net;
    }

    /**
     * Returns Discount Taxes of Coupon Taxes for Views
     *
     * @return array
     */
    public function getDiscountTaxes()
    {
        $taxes = array_map(function ($value) {
            return -1 * $value;
        }, $this->getCouponTaxes());

        return $taxes;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Product $newProduct
     */
    public function addProduct($newProduct)
    {
        $id = $newProduct->getId();
        $product = $this->products[$id];

        if ($product) {
            // change $newproduct in cart
            $this->changeProduct($product, $newProduct);
            $this->calcAll();
        } else {
            // $newproduct is not in cart
            $newProduct->setCart($this);
            $this->products[$id] = $newProduct;
            $this->calcAll();
            $this->addServiceAttributes($newProduct);
        }
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\Product $newProduct
     *
     * @internal param $id
     * @internal param $newQuantity
     */
    public function changeProduct($product, $newProduct)
    {
        $newQuantity = $product->getQuantity() + $newProduct->getQuantity();

        $this->subCount($product->getQuantity());
        $this->subGross($product->getGross());
        $this->subNet($product->getNet());
        $this->subTax($product->getTax(), $product->getTaxClass());

        // if the new product has a variant then change it in product
        if ($newProduct->getBeVariants()) {
            $product->addBeVariants($newProduct->getBeVariants());
        }

        $product->changeQuantity($newQuantity);

        $this->addCount($product->getQuantity());
        $this->addGross($product->getGross());
        $this->addNet($product->getNet());
        $this->addTax($product->getTax(), $product->getTaxClass());

        //update all service attributes
        $this->updateServiceAttributes();
    }

    /**
     * @param $productQuantityArray
     * @internal param $id
     * @internal param $newQuantity
     */
    public function changeProductsQuantity($productQuantityArray)
    {
        foreach ($productQuantityArray as $productPuid => $quantity) {
            $product = $this->products[$productPuid];

            if ($product) {
                if (is_array($quantity)) {
                    $this->subCount($product->getQuantity());
                    $this->subGross($product->getGross());
                    $this->subNet($product->getNet());
                    $this->subTax($product->getTax(), $product->getTaxClass());

                    $product->changeVariantsQuantity($quantity);

                    $this->addCount($product->getQuantity());
                    $this->addGross($product->getGross());
                    $this->addNet($product->getNet());
                    $this->addTax($product->getTax(), $product->getTaxClass());
                } else {
                    // only run, if quantity was realy changed
                    if ($product->getQuantity() != $quantity) {
                        $this->subCount($product->getQuantity());
                        $this->subGross($product->getGross());
                        $this->subNet($product->getNet());
                        $this->subTax($product->getTax(), $product->getTaxClass());

                        $product->changeQuantity($quantity);

                        $this->addCount($product->getQuantity());
                        $this->addGross($product->getGross());
                        $this->addNet($product->getNet());
                        $this->addTax($product->getTax(), $product->getTaxClass());
                    }
                }
            }

            //update all service attributes
            $this->updateServiceAttributes();
        }
    }

    /**
     * @param array|string $productParams
     *
     * @return bool
     */
    public function removeProductById($productParams)
    {
        if (is_array($productParams)) {
            $productId = key($productParams);
            $product = $this->products[$productId];
            if ($product) {
                $this->removeproduct($product, $productParams[$productId]);
            } else {
                return -1;
            }
        } elseif (is_string($productParams)) {
            $product = $this->products[$productParams];
            if ($product) {
                $this->removeproduct($product);
            } else {
                return -1;
            }
        }

        $this->updateServiceAttributes();

        return true;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     *
     * @param array $productVariantIds
     *
     * @return bool
     */
    public function removeproduct($product, $productVariantIds = null)
    {
        if (is_array($productVariantIds)) {
            $product->removeBeVariants($productVariantIds);

            if (!$product->getBeVariants()) {
                unset($this->products[$product->getId()]);
            }

            $this->calcAll();
        } else {
            $this->subCount($product->getQuantity());
            $this->subGross($product->getGross());
            $this->subNet($product->getNet());
            $this->subTax($product->getTax(), $product->getTaxClass());

            unset($this->products[$product->getId()]);
        }

        return true;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Product $newproduct
     */
    protected function addServiceAttributes($newproduct)
    {
        if ($this->maxServiceAttr1 > $newproduct->getServiceAttribute1()) {
            $this->maxServiceAttr1 = $newproduct->getServiceAttribute1();
        }
        if ($this->maxServiceAttr2 > $newproduct->getServiceAttribute2()) {
            $this->maxServiceAttr2 = $newproduct->getServiceAttribute2();
        }
        if ($this->maxServiceAttr3 > $newproduct->getServiceAttribute3()) {
            $this->maxServiceAttr3 = $newproduct->getServiceAttribute3();
        }

        $this->sumServiceAttr1 += $newproduct->getServiceAttribute1() * $newproduct->getQuantity();
        $this->sumServiceAttr2 += $newproduct->getServiceAttribute2() * $newproduct->getQuantity();
        $this->sumServiceAttr3 += $newproduct->getServiceAttribute3() * $newproduct->getQuantity();
    }

    /**
     */
    protected function updateServiceAttributes()
    {
        $this->maxServiceAttr1 = 0.0;
        $this->maxServiceAttr2 = 0.0;
        $this->maxServiceAttr3 = 0.0;
        $this->sumServiceAttr1 = 0.0;
        $this->sumServiceAttr2 = 0.0;
        $this->sumServiceAttr3 = 0.0;

        foreach ($this->products as $key => $product) {
            if ($this->maxServiceAttr1 > $product->getServiceAttribute1()) {
                $this->maxServiceAttr1 = $product->getServiceAttribute1();
            }
            if ($this->maxServiceAttr2 > $product->getServiceAttribute2()) {
                $this->maxServiceAttr2 = $product->getServiceAttribute2();
            }
            if ($this->maxServiceAttr3 > $product->getServiceAttribute3()) {
                $this->maxServiceAttr3 = $product->getServiceAttribute3();
            }

            $this->sumServiceAttr1 += $product->getServiceAttribute1() * $product->getQuantity();
            $this->sumServiceAttr2 += $product->getServiceAttribute2() * $product->getQuantity();
            $this->sumServiceAttr3 += $product->getServiceAttribute3() * $product->getQuantity();
        }
    }

    /**
     * @return float
     */
    public function getMaxServiceAttribute1()
    {
        return $this->maxServiceAttr1;
    }

    /**
     * @return float
     */
    public function getMaxServiceAttribute2()
    {
        return $this->maxServiceAttr2;
    }

    /**
     * @return float
     */
    public function getMaxServiceAttribute3()
    {
        return $this->maxServiceAttr3;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute1()
    {
        return $this->sumServiceAttr1;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute2()
    {
        return $this->sumServiceAttr2;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute3()
    {
        return $this->sumServiceAttr3;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Service $shipping
     */
    public function changeShipping(\Extcode\Cart\Domain\Model\Cart\Service $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Service $payment
     */
    public function changePayment(\Extcode\Cart\Domain\Model\Cart\Service $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Service[] $specials
     */
    public function changeSpecials($specials)
    {
        $this->specials = $specials;
    }

    /**
     */
    protected function calcAll()
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     */
    protected function calcCount()
    {
        $this->count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addCount($product->getQuantity());
            }
        }
    }

    /**
     */
    protected function calcGross()
    {
        $this->gross = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addGross($product->getGross());
            }
        }
    }

    /**
     */
    protected function calcNet()
    {
        $this->net = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addNet($product->getNet());
            }
        }
    }

    /**
     */
    protected function calcTax()
    {
        $this->taxes = [];
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addTax($product->getTax(), $product->getTaxClass());
            }
        }
    }

    /**
     */
    public function reCalc()
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcNet();
        $this->calcTax();

        $this->updateServiceAttributes();
    }

    /**
     * @return array
     */
    public function getAdditionalArray()
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditionalArray($additional)
    {
        $this->additional = $additional;
    }

    /**
     */
    public function unsetAdditionalArray()
    {
        $this->additional = [];
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
     * @param string $key
     */
    public function unsetAdditional($key)
    {
        if ($this->additional[$key]) {
            unset($this->additional[$key]);
        }
    }

    /**
     * @param int $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return float
     */
    public function getSubtotalGross()
    {
        return $this->gross - $this->getCouponGross();
    }

    /**
     * @return float
     */
    public function getTotalGross()
    {
        return $this->gross - $this->getCouponGross() + $this->getServiceGross();
    }

    /**
     * @return float
     */
    public function getSubtotalNet()
    {
        return $this->net - $this->getCouponNet();
    }

    /**
     * @return float
     */
    public function getTotalNet()
    {
        return $this->net - $this->getCouponNet() + $this->getServiceNet();
    }

    /**
     * @return bool
     */
    protected function isOrderable()
    {
        $isOrderable = true;

        if ($this->products) {
            foreach ($this->products as $product) {
                if (!$product->getQuantityIsInRange()) {
                    $isOrderable = false;
                    break;
                }
            }
        } else {
            $isOrderable = false;
        }

        return $isOrderable;
    }

    /**
     * @return bool
     */
    public function getIsOrderable()
    {
        return $this->isOrderable();
    }

    /**
     * @return string
     */
    public function getBillingCountry()
    {
        return $this->billingCountry;
    }

    /**
     * @param string $billingCountry
     */
    public function setBillingCountry($billingCountry)
    {
        $this->billingCountry = $billingCountry;
    }

    /**
     * @return bool
     */
    public function isShippingSameAsBilling(): bool
    {
        return $this->shippingSameAsBilling;
    }

    /**
     * @param bool $shippingSameAsBilling
     */
    public function setShippingSameAsBilling(bool $shippingSameAsBilling): void
    {
        $this->shippingSameAsBilling = $shippingSameAsBilling;
    }

    /**
     * @return string
     */
    public function getShippingCountry()
    {
        if ($this->isShippingSameAsBilling()) {
            return $this->getBillingCountry();
        }

        return $this->shippingCountry;
    }

    /**
     * @param string $shippingCountry
     */
    public function setShippingCountry($shippingCountry)
    {
        $this->shippingCountry = $shippingCountry;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        if (!empty($this->shippingCountry)) {
            return $this->shippingCountry;
        }

        return $this->billingCountry;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCurrencyTranslation()
    {
        return $this->currencyTranslation;
    }

    /**
     * @param string $currencyTranslation
     */
    public function setCurrencyTranslation($currencyTranslation)
    {
        $this->currencyTranslation = $currencyTranslation;
    }

    /**
     * @return string
     */
    public function getCurrencySign()
    {
        return $this->currencySign;
    }

    /**
     * @param string $currencySign
     */
    public function setCurrencySign($currencySign)
    {
        $this->currencySign = $currencySign;
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function translatePrice($price)
    {
        $price = $price / $this->getCurrencyTranslation();
        $price = round($price * 100.0) / 100.0;

        return $price;
    }
}
