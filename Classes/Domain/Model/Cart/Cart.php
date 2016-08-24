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
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Cart
{

    /**
     * Tax Classes
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass[]
     */
    private $taxClasses;

    /**
     * Net
     *
     * @var float
     */
    private $net;

    /**
     * Gross
     *
     * @var float
     */
    private $gross;

    /**
     * Taxes
     *
     * @var array
     */
    private $taxes;

    /**
     * Count
     *
     * @var int
     */
    private $count;

    /**
     * Products
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Product[]
     */
    private $products;

    /**
     * Shipping
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Shipping
     */
    private $shipping;

    /**
     * Payment
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Payment
     */
    private $payment;

    /**
     * Specials
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Special[]
     */
    private $specials;

    /**
     * Max Service Attribute 1
     *
     * @var float
     */
    private $maxServiceAttr1 = 0.0;

    /**
     * Max Service Attribute 2
     *
     * @var float
     */
    private $maxServiceAttr2 = 0.0;

    /**
     * Max Service Attribute 3
     *
     * @var float
     */
    private $maxServiceAttr3 = 0.0;

    /**
     * Sum Service Attribute 1
     *
     * @var float
     */
    private $sumServiceAttr1 = 0.0;

    /**
     * Sum Service Attribute 2
     *
     * @var float
     */
    private $sumServiceAttr2 = 0.0;

    /**
     * Sum Service Attribute 3
     *
     * @var float
     */
    private $sumServiceAttr3 = 0.0;

    /**
     * Is Net Cart
     *
     * @var bool
     */
    private $isNetCart;

    /**
     * Order Number
     *
     * @var string
     */
    private $orderNumber;

    /**
     * Invoice Number
     *
     * @var string
     */
    private $invoiceNumber;

    /**
     * Additional
     *
     * @var array
     */
    private $additional = [];

    /**
     * Order Id
     *
     * @var int
     */
    private $orderId;

    /**
     * Coupon
     *
     * @var \Extcode\Cart\Domain\Model\Cart\CartCouponInterface[]
     */
    private $coupons = [];

    /**
     * __construct
     *
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass[] $taxClasses
     * @param bool $isNetCart
     *
     * @return Cart
     */
    public function __construct(array $taxClasses, $isNetCart = false)
    {
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
            'additional'
        ];
    }

    /**
     * __wakeup
     *
     * @return void
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
     *
     * @return void
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
     * @return void
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
     * @return void
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
     *
     * @return void
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
     *
     * @return void
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * @param $net
     *
     * @return void
     */
    public function subNet($net)
    {
        $this->net -= $net;
    }

    /**
     * @param $gross
     *
     * @return void
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
     *
     * @return void
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    /**
     * @param $gross
     *
     * @return void
     */
    public function subGross($gross)
    {
        $this->gross -= $gross;
    }

    /**
     * @param float $tax
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     *
     * @return void
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
     *
     * @return void
     */
    public function setTax($taxClassId, $tax)
    {
        $this->taxes[$taxClassId] = $tax;
    }

    /**
     * @param float $tax
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     *
     * @return void
     */
    public function subTax($tax, $taxClass)
    {
        $this->taxes[$taxClass->getId()] -= $tax;
    }

    /**
     * @param $count
     *
     * @return void
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
     *
     * @return void
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @param $count
     *
     * @return void
     */
    public function subCount($count)
    {
        $this->count -= $count;
    }

    /**
     * @return Shipping
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * @param Shipping $shipping
     *
     * @return void
     */
    public function setShipping($shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Payment $payment
     *
     * @return void
     */
    public function setPayment($payment)
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
     *
     * @return void
     */
    public function addSpecial($newSpecial)
    {
        $this->specials[$newSpecial->getId()] = $newSpecial;
    }

    /**
     * @param Special $special
     * @return void
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
        };

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
            $cartArray['payment'] = $this->shipping->getName();
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
        json_encode($this->toArray());
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
        $taxes = array_map(function($value) { return -1 * $value; }, $this->getCouponTaxes());

        return $taxes;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Product $newProduct
     *
     * @return void
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
     *
     * @return void
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
     * @return void
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
     *
     * @return void
     */
    private function addServiceAttributes($newproduct)
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
     * @return void
     */
    private function updateServiceAttributes()
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

            $this->sumServiceAttr1 = $product->getServiceAttribute1() * $product->getQuantity();
            $this->sumServiceAttr2 = $product->getServiceAttribute2() * $product->getQuantity();
            $this->sumServiceAttr3 = $product->getServiceAttribute3() * $product->getQuantity();
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
     * @param \Extcode\Cart\Domain\Model\Cart\Shipping $shipping
     *
     * @return void
     */
    public function changeShipping(\Extcode\Cart\Domain\Model\Cart\Shipping $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Payment $payment
     *
     * @return void
     */
    public function changePayment(\Extcode\Cart\Domain\Model\Cart\Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Special[] $specials
     *
     * @return void
     */
    public function changeSpecials($specials)
    {
        $this->specials = $specials;
    }

    /**
     * @return void
     */
    private function calcAll()
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @return void
     */
    private function calcCount()
    {
        $this->count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addCount($product->getQuantity());
            }
        }
    }

    /**
     * @return void
     */
    private function calcGross()
    {
        $this->gross = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addGross($product->getGross());
            }
        }
    }

    /**
     * @return void
     */
    private function calcNet()
    {
        $this->net = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addNet($product->getNet());
            }
        }
    }

    /**
     * @return void
     */
    private function calcTax()
    {
        $this->taxes = [];
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addTax($product->getTax(), $product->getTaxClass());
            }
        }
    }

    /**
     * @return void
     */
    public function reCalc()
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcNet();
        $this->calcTax();
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
     * @return void
     */
    public function setAdditionalArray($additional)
    {
        $this->additional = $additional;
    }

    /**
     * @return void
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
     *
     * @return void
     */
    public function setAdditional($key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return void
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
}
