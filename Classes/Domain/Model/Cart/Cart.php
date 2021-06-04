<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Cart
{
    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass[]
     */
    protected $taxClasses;

    /**
     * @var float
     */
    protected $net;

    /**
     * @var float
     */
    protected $gross;

    /**
     * @var array
     */
    protected $taxes = [];

    /**
     * @var int
     */
    protected $count;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Product[]
     */
    protected $products = [];

    /**
     * @var ServiceInterface
     */
    protected $shipping;

    /**
     * @var ServiceInterface
     */
    protected $payment;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Special[]
     */
    protected $specials;

    /**
     * @var float
     */
    protected $maxServiceAttr1 = 0.0;

    /**
     * @var float
     */
    protected $maxServiceAttr2 = 0.0;

    /**
     * @var float
     */
    protected $maxServiceAttr3 = 0.0;

    /**
     * @var float
     */
    protected $sumServiceAttr1 = 0.0;

    /**
     * @var float
     */
    protected $sumServiceAttr2 = 0.0;

    /**
     * @var float
     */
    protected $sumServiceAttr3 = 0.0;

    /**
     * @var bool
     */
    protected $isNetCart;

    /**
     * @var string
     */
    protected $orderNumber = '';

    /**
     * @var string
     */
    protected $invoiceNumber = '';

    /**
     * @var array
     */
    protected $additional = [];

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\CartCouponInterface[]
     */
    protected $coupons = [];

    /**
     * @var string
     */
    protected $billingCountry = '';

    /**
     * @var bool
     */
    protected $shippingSameAsBilling = true;

    /**
     * @var string
     */
    protected $shippingCountry = '';

    /**
     * @var string
     */
    protected $currencyCode = '';

    /**
     * @var string
     */
    protected $currencySign = '';

    /**
     * @var float
     */
    protected $currencyTranslation = 1.0;

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass[] $taxClasses
     * @param bool $isNetCart
     * @param string $currencyCode
     * @param string $currencySign
     * @param float $currencyTranslation
     */
    public function __construct(
        array $taxClasses,
        bool $isNetCart = false,
        string $currencyCode = 'EUR',
        string $currencySign = '€',
        float $currencyTranslation = 1.00
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
    public function setOrderNumber(string $orderNumber): void
    {
        if (($this->orderNumber) && ($this->orderNumber !== $orderNumber)) {
            throw new \LogicException(
                'You can not redeclare the order number of your cart.',
                1413969668
            );
        }

        $this->orderNumber = $orderNumber;
    }

    /**
     * Allow to reset the Order Number
     *
     * The order number should only be reset in exceptional cases.
     * For example, when the shopping cart is reloaded after a canceled order.
     */
    public function resetOrderNumber(): void
    {
        $this->orderNumber = '';
    }

    /**
     * Gets Order Number
     *
     * @return string
     */
    public function getOrderNumber(): string
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
    public function setInvoiceNumber(string $invoiceNumber): void
    {
        if (($this->invoiceNumber) && ($this->invoiceNumber !== $invoiceNumber)) {
            throw new \LogicException(
                'You can not redeclare the invoice number of your cart.',
                1413969712
            );
        }

        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * Allow to reset the Invoice Number
     *
     * The invoice number should only be reset in exceptional cases.
     * For example, when the shopping cart is reloaded after a canceled order.
     */
    public function resetInvoiceNumber(): void
    {
        $this->orderNumber = '';
    }

    /**
     * Gets Invoice Number
     *
     * @return string
     */
    public function getInvoiceNumber(): string
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
        if (array_key_exists($taxClass->getId(), $this->taxes)) {
            $this->taxes[$taxClass->getId()] += $tax;
        } else {
            $this->taxes[$taxClass->getId()] = $tax;
        }
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
            $paymentTaxes = $this->payment->getTaxes();
            foreach ($paymentTaxes as $paymentTax) {
                $taxes[$paymentTax['taxClassId']] += $paymentTax['tax'];
            }
        }
        if ($this->shipping) {
            $shippingTaxes = $this->shipping->getTaxes();
            foreach ($shippingTaxes as $shippingTax) {
                $taxes[$shippingTax['taxClassId']] += $shippingTax['tax'];
            }
        }
        if ($this->specials) {
            foreach ($this->specials as $special) {
                $specialTaxes = $special->getTaxes();
                foreach ($specialTaxes as $specialTax) {
                    $taxes[$specialTax['taxClassId']] += $specialTax['tax'];
                }
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

        $serviceTaxes = $this->getServiceTaxes();
        foreach ($serviceTaxes as $serviceTaxClassId => $serviceTax) {
            $taxes[$serviceTaxClassId] += $serviceTax;
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
    public function toArray(): array
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
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Return Coupons
     *
     * @return array
     */
    public function getCoupons(): array
    {
        return $this->coupons;
    }

    /**
     * @return bool
     */
    protected function areCouponsCombinable(): bool
    {
        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if (!$coupon->getIsCombinable()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param CartCouponInterface $coupon
     *
     * @return int
     */
    public function addCoupon(CartCouponInterface $coupon): int
    {
        if (!empty($this->coupons) && array_key_exists($coupon->getCode(), $this->coupons)) {
            return -1;
        }

        if (!empty($this->coupons) && (!$this->areCouponsCombinable() || !$coupon->getIsCombinable())) {
            return -2;
        }

        $coupon->setCart($this);
        $this->coupons[$coupon->getCode()] = $coupon;

        return 1;
    }

    /**
     * @param string $couponCode
     *
     * @return int
     */
    public function removeCoupon(string $couponCode): int
    {
        if (!$this->coupons[$couponCode]) {
            return -1;
        }

        unset($this->coupons[$couponCode]);

        return 1;
    }

    /**
     * @return float
     */
    public function getCouponGross(): float
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
     * @return float
     */
    public function getCouponNet(): float
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
     * @return array
     */
    public function getCouponTaxes(): array
    {
        $taxes = [];

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->getIsUseable()) {
                    $tax = $coupon->getTax();
                    if (array_key_exists($coupon->getTaxClass()->getId(), $taxes)) {
                        $taxes[$coupon->getTaxClass()->getId()] += $tax;
                    } else {
                        $taxes[$coupon->getTaxClass()->getId()] = $tax;
                    }
                }
            }
        }

        return $taxes;
    }

    /**
     * @return float
     */
    public function getDiscountGross(): float
    {
        $gross = -1 * $this->getCouponGross();

        return $gross;
    }

    /**
     * @return float
     */
    public function getDiscountNet(): float
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
     * @param Product $newProduct
     */
    public function addProduct(Product $newProduct)
    {
        $id = $newProduct->getId();

        if (!empty($this->products) && array_key_exists($id, $this->products)) {
            // change $newproduct in cart
            $this->changeProduct($this->products[$id], $newProduct);
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
     * @param Product $product
     * @param Product $newProduct
     */
    public function changeProduct(Product $product, Product $newProduct)
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
     * @param array $productQuantityArray
     */
    public function changeProductsQuantity(array $productQuantityArray)
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
     * @param array $products
     *
     * @return int
     */
    public function removeProductByIds(array $products): int
    {
        $productId = key($products);
        $product = $this->products[$productId];
        if ($product) {
            $this->removeProduct($product, $products[$productId]);
        } else {
            return -1;
        }

        $this->updateServiceAttributes();

        return true;
    }

    /**
     * @param string $product
     *
     * @return int
     */
    public function removeProductById(string $product): int
    {
        $product = $this->products[$product];
        if ($product) {
            $this->removeProduct($product);
        } else {
            return -1;
        }

        $this->updateServiceAttributes();

        return true;
    }

    /**
     * @param Product $product
     * @param array $productVariantIds
     *
     * @return bool
     */
    public function removeProduct(Product $product, array $productVariantIds = [])
    {
        if (is_array($productVariantIds) && !empty($productVariantIds)) {
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
     * @param Product $newProduct
     */
    protected function addServiceAttributes(Product $newProduct)
    {
        if ($this->maxServiceAttr1 > $newProduct->getServiceAttribute1()) {
            $this->maxServiceAttr1 = $newProduct->getServiceAttribute1();
        }
        if ($this->maxServiceAttr2 > $newProduct->getServiceAttribute2()) {
            $this->maxServiceAttr2 = $newProduct->getServiceAttribute2();
        }
        if ($this->maxServiceAttr3 > $newProduct->getServiceAttribute3()) {
            $this->maxServiceAttr3 = $newProduct->getServiceAttribute3();
        }

        $this->sumServiceAttr1 += $newProduct->getServiceAttribute1() * $newProduct->getQuantity();
        $this->sumServiceAttr2 += $newProduct->getServiceAttribute2() * $newProduct->getQuantity();
        $this->sumServiceAttr3 += $newProduct->getServiceAttribute3() * $newProduct->getQuantity();
    }

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
    public function getMaxServiceAttribute1(): float
    {
        return $this->maxServiceAttr1;
    }

    /**
     * @return float
     */
    public function getMaxServiceAttribute2(): float
    {
        return $this->maxServiceAttr2;
    }

    /**
     * @return float
     */
    public function getMaxServiceAttribute3(): float
    {
        return $this->maxServiceAttr3;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute1(): float
    {
        return $this->sumServiceAttr1;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute2(): float
    {
        return $this->sumServiceAttr2;
    }

    /**
     * @return float
     */
    public function getSumServiceAttribute3(): float
    {
        return $this->sumServiceAttr3;
    }

    /**
     * @param Service $shipping
     */
    public function changeShipping(Service $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @param Service $payment
     */
    public function changePayment(Service $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @param Service[] $specials
     */
    public function changeSpecials(array $specials)
    {
        $this->specials = $specials;
    }

    protected function calcAll()
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    protected function calcCount()
    {
        $this->count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addCount($product->getQuantity());
            }
        }
    }

    protected function calcGross()
    {
        $this->gross = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addGross($product->getGross());
            }
        }
    }

    protected function calcNet()
    {
        $this->net = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addNet($product->getNet());
            }
        }
    }

    protected function calcTax()
    {
        $this->taxes = [];
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addTax($product->getTax(), $product->getTaxClass());
            }
        }
    }

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
    public function getAdditionalArray(): array
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditionalArray(array $additional)
    {
        $this->additional = $additional;
    }

    public function unsetAdditionalArray()
    {
        $this->additional = [];
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getAdditional(string $key)
    {
        return $this->additional[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAdditional(string $key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function unsetAdditional(string $key)
    {
        if ($this->additional[$key]) {
            unset($this->additional[$key]);
        }
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @return float
     */
    public function getSubtotalGross(): float
    {
        return $this->gross - $this->getCouponGross();
    }

    /**
     * @return float
     */
    public function getTotalGross(): float
    {
        return $this->gross - $this->getCouponGross() + $this->getServiceGross();
    }

    /**
     * @return float
     */
    public function getSubtotalNet(): float
    {
        return $this->net - $this->getCouponNet();
    }

    /**
     * @return float
     */
    public function getTotalNet(): float
    {
        return $this->net - $this->getCouponNet() + $this->getServiceNet();
    }

    /**
     * @return bool
     */
    protected function isOrderable(): bool
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
    public function getIsOrderable(): bool
    {
        return $this->isOrderable();
    }

    /**
     * @return string
     */
    public function getBillingCountry(): string
    {
        return $this->billingCountry;
    }

    /**
     * @param string $billingCountry
     */
    public function setBillingCountry(string $billingCountry)
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
    public function setShippingSameAsBilling(bool $shippingSameAsBilling)
    {
        $this->shippingSameAsBilling = $shippingSameAsBilling;
    }

    /**
     * @return string
     */
    public function getShippingCountry(): string
    {
        if ($this->isShippingSameAsBilling()) {
            return $this->getBillingCountry();
        }

        return $this->shippingCountry;
    }

    /**
     * @param string $shippingCountry
     */
    public function setShippingCountry(string $shippingCountry)
    {
        $this->shippingCountry = $shippingCountry;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        if (!empty($this->shippingCountry)) {
            return $this->shippingCountry;
        }

        return $this->billingCountry;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode(string $currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return float
     */
    public function getCurrencyTranslation(): float
    {
        return $this->currencyTranslation;
    }

    /**
     * @param float $currencyTranslation
     */
    public function setCurrencyTranslation(float $currencyTranslation)
    {
        $this->currencyTranslation = $currencyTranslation;
    }

    /**
     * @return string
     */
    public function getCurrencySign(): string
    {
        return $this->currencySign;
    }

    /**
     * @param string $currencySign
     */
    public function setCurrencySign(string $currencySign)
    {
        $this->currencySign = $currencySign;
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function translatePrice($price): float
    {
        $price = $price / $this->getCurrencyTranslation();
        $price = round($price * 100.0) / 100.0;

        return $price;
    }
}
