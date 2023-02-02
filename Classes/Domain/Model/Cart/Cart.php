<?php

declare(strict_types=1);

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
     * @var TaxClass[]
     */
    protected array $taxClasses;

    protected float $net;

    protected float $gross;

    protected array $taxes = [];

    protected int $count;

    /**
     * @var Product[]
     */
    protected array $products = [];

    protected ?ServiceInterface $shipping = null;

    protected ?ServiceInterface $payment = null;

    /**
     * @var Special[]
     */
    protected ?array $specials = null;

    protected float $maxServiceAttr1 = 0.0;

    protected float $maxServiceAttr2 = 0.0;

    protected float $maxServiceAttr3 = 0.0;

    protected float $sumServiceAttr1 = 0.0;

    protected float $sumServiceAttr2 = 0.0;

    protected float $sumServiceAttr3 = 0.0;

    protected bool $isNetCart = false;

    protected string $orderNumber = '';

    protected string $invoiceNumber = '';

    protected array $additional = [];

    protected int $orderId;

    /**
     * @var CartCouponInterface[]
     */
    protected array $coupons = [];

    protected string $billingCountry = '';

    protected bool $shippingSameAsBilling = true;

    protected string $shippingCountry = '';

    protected string $currencyCode = '';

    protected string $currencySign = '';

    protected float $currencyTranslation = 1.0;

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
     * @return TaxClass[]
     */
    public function getTaxClasses(): array
    {
        return $this->taxClasses;
    }

    /**
     * @param TaxClass[] $taxClasses
     */
    public function setTaxClasses(array $taxClasses): void
    {
        $this->taxClasses = $taxClasses;

        if ($this->products) {
            foreach ($this->products as $product) {
                $taxClassId = $product->getTaxClass()->getId();
                $product->setTaxClass($taxClasses[$taxClassId]);
            }
        }
    }

    public function getTaxClass(int $taxClassId): TaxClass
    {
        return $this->taxClasses[$taxClassId];
    }

    public function setIsNetCart(bool $isNetCart): void
    {
        $this->isNetCart = $isNetCart;
    }

    public function isNetCart(): bool
    {
        return $this->isNetCart;
    }

    /**
     * Sets Order Number if no Order Number is given else throws an exception
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

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * Sets Invoice Number if no Invoice Number is given else throws an exception
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

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function addNet(float $net): void
    {
        $this->net += $net;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    public function setNet(float $net): void
    {
        $this->net = $net;
    }

    public function subNet(float $net): void
    {
        $this->net -= $net;
    }

    public function addGross(float $gross): void
    {
        $this->gross += $gross;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function setGross(float $gross): void
    {
        $this->gross = $gross;
    }

    public function subGross(float $gross): void
    {
        $this->gross -= $gross;
    }

    public function addTax(float $tax, TaxClass $taxClass): void
    {
        if (array_key_exists($taxClass->getId(), $this->taxes)) {
            $this->taxes[$taxClass->getId()] += $tax;
        } else {
            $this->taxes[$taxClass->getId()] = $tax;
        }
    }

    public function getTaxes(): array
    {
        return $this->taxes;
    }

    public function getServiceTaxes(): array
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

    public function getSubtotalTaxes(): array
    {
        $taxes = $this->taxes;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->isUseable()) {
                    $tax = $coupon->getTax();
                    $taxes[$coupon->getTaxClass()->getId()] -= $tax;
                }
            }
        }

        return $taxes;
    }

    public function getTotalTaxes(): array
    {
        $taxes = $this->getSubtotalTaxes();

        $serviceTaxes = $this->getServiceTaxes();
        foreach ($serviceTaxes as $taxClassId => $tax) {
            $taxes[$taxClassId] += $tax;
        }

        $couponTaxes = $this->getCouponTaxes();
        foreach ($couponTaxes as $taxClassId => $tax) {
            $taxes[$taxClassId] -= $tax;
        }

        return $taxes;
    }

    public function setTax(int $taxClassId, float $tax): void
    {
        $this->taxes[$taxClassId] = $tax;
    }

    public function subTax(float $tax, TaxClass $taxClass): void
    {
        $this->taxes[$taxClass->getId()] -= $tax;
    }

    public function addCount(int $count): void
    {
        $this->count += $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function subCount(int $count): void
    {
        $this->count -= $count;
    }

    public function getCountPhysicalProducts(): int
    {
        $count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                if (!$product->isVirtualProduct()) {
                    $count += $product->getQuantity();
                }
            }
        }

        return $count;
    }

    public function getCountVirtualProducts(): int
    {
        $count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                if ($product->isVirtualProduct()) {
                    $count += $product->getQuantity();
                }
            }
        }

        return $count;
    }

    public function getShipping(): ?ServiceInterface
    {
        return $this->shipping;
    }

    public function setShipping(ServiceInterface $shipping): void
    {
        $this->shipping = $shipping;
    }

    public function getPayment(): ?ServiceInterface
    {
        return $this->payment;
    }

    public function setPayment(ServiceInterface $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @return Special[]
     */
    public function getSpecials(): array
    {
        return $this->specials;
    }

    /**
     * @param Special $newSpecial
     */
    public function addSpecial($newSpecial): void
    {
        $this->specials[$newSpecial->getId()] = $newSpecial;
    }

    /**
     * @param Special $special
     */
    public function removeSpecial($special): void
    {
        unset($this->specials[$special->getId()]);
    }

    public function getServiceNet(): float
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

    public function getServiceGross(): float
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
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getProductById(string $productId): ?Product
    {
        if (isset($this->products[$productId])) {
            return $this->products[$productId];
        }

        return null;
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
            'additional' => $this->additional,
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

    protected function areCouponsCombinable(): bool
    {
        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if (!$coupon->isCombinable()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function addCoupon(CartCouponInterface $coupon): int
    {
        if (!empty($this->coupons) && array_key_exists($coupon->getCode(), $this->coupons)) {
            return -1;
        }

        if (!empty($this->coupons) && (!$this->areCouponsCombinable() || !$coupon->isCombinable())) {
            return -2;
        }

        $coupon->setCart($this);
        $this->coupons[$coupon->getCode()] = $coupon;

        return 1;
    }

    public function removeCoupon(string $couponCode): int
    {
        if (!$this->coupons[$couponCode]) {
            return -1;
        }

        unset($this->coupons[$couponCode]);

        return 1;
    }

    public function getCouponGross(): float
    {
        $gross = 0.0;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->isUseable()) {
                    $gross += $coupon->getGross();
                }
            }
        }

        return $gross;
    }

    public function getCouponNet(): float
    {
        $net = 0.0;

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->isUseable()) {
                    $net += $coupon->getNet();
                }
            }
        }

        return $net;
    }

    public function getCouponTaxes(): array
    {
        $taxes = [];

        if ($this->coupons) {
            foreach ($this->coupons as $coupon) {
                if ($coupon->isUseable()) {
                    // TODO block will be removed / replaced by a getTaxes() method in v9.x for TYPO3 v12 and v11.
                    $tax = $coupon->getTax();
                    $taxClassId = $coupon->getTaxClass()->getId();
                    if (array_key_exists($taxClassId, $taxes)) {
                        $taxes[$taxClassId] += $tax;
                    } else {
                        $taxes[$taxClassId] = $tax;
                    }

                    if (method_exists($coupon, 'getTaxes')) {
                        foreach ($coupon->getTaxes() as $taxClassId => $tax) {
                            if (array_key_exists($taxClassId, $taxes)) {
                                $taxes[$taxClassId] += $tax;
                            } else {
                                $taxes[$taxClassId] = $tax;
                            }
                        }
                    }
                }
            }
        }

        return $taxes;
    }

    public function getDiscountGross(): float
    {
        $gross = -1 * $this->getCouponGross();

        return $gross;
    }

    public function getDiscountNet(): float
    {
        $net = -1 * $this->getCouponNet();

        return $net;
    }

    /**
     * Returns Discount Taxes of Coupon Taxes for Views
     */
    public function getDiscountTaxes(): array
    {
        $taxes = array_map(function ($value) {
            return -1 * $value;
        }, $this->getCouponTaxes());

        return $taxes;
    }

    public function addProduct(Product $newProduct): void
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

    public function changeProduct(Product $product, Product $newProduct): void
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

    public function changeProductsQuantity(array $productQuantityArray): void
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

    public function removeProductByIds(array $products): bool
    {
        $productId = key($products);

        if (!isset($this->products[$productId])) {
            return false;
        }

        $product = $this->products[$productId];
        if ($product) {
            $this->removeProduct($product, $products[$productId]);
        } else {
            return false;
        }

        $this->updateServiceAttributes();

        return true;
    }

    public function removeProductById(string $productId): bool
    {
        if (!isset($this->products[$productId])) {
            return false;
        }

        $this->removeProduct($this->products[$productId]);
        $this->updateServiceAttributes();

        return true;
    }

    public function removeProduct(Product $product, array $productVariantIds = []): bool
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

    protected function addServiceAttributes(Product $newProduct): void
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

    protected function updateServiceAttributes(): void
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

    public function getMaxServiceAttribute1(): float
    {
        return $this->maxServiceAttr1;
    }

    public function getMaxServiceAttribute2(): float
    {
        return $this->maxServiceAttr2;
    }

    public function getMaxServiceAttribute3(): float
    {
        return $this->maxServiceAttr3;
    }

    public function getSumServiceAttribute1(): float
    {
        return $this->sumServiceAttr1;
    }

    public function getSumServiceAttribute2(): float
    {
        return $this->sumServiceAttr2;
    }

    public function getSumServiceAttribute3(): float
    {
        return $this->sumServiceAttr3;
    }

    public function changeShipping(Service $shipping): void
    {
        $this->shipping = $shipping;
    }

    public function changePayment(Service $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @param Service[] $specials
     */
    public function changeSpecials(array $specials): void
    {
        $this->specials = $specials;
    }

    protected function calcAll(): void
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    protected function calcCount(): void
    {
        $this->count = 0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addCount($product->getQuantity());
            }
        }
    }

    protected function calcGross(): void
    {
        $this->gross = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addGross($product->getGross());
            }
        }
    }

    protected function calcNet(): void
    {
        $this->net = 0.0;
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addNet($product->getNet());
            }
        }
    }

    protected function calcTax(): void
    {
        $this->taxes = [];
        if ($this->products) {
            foreach ($this->products as $product) {
                $this->addTax($product->getTax(), $product->getTaxClass());
            }
        }
    }

    public function reCalc(): void
    {
        $this->calcCount();
        $this->calcGross();
        $this->calcNet();
        $this->calcTax();

        $this->updateServiceAttributes();
    }

    public function getAdditionalArray(): array
    {
        return $this->additional;
    }

    public function setAdditionalArray(array $additional): void
    {
        $this->additional = $additional;
    }

    public function unsetAdditionalArray(): void
    {
        $this->additional = [];
    }

    /**
     * @return mixed
     */
    public function getAdditional(string $key)
    {
        return $this->additional[$key];
    }

    /**
     * @param mixed $value
     */
    public function setAdditional(string $key, $value): void
    {
        $this->additional[$key] = $value;
    }

    public function unsetAdditional(string $key): void
    {
        if ($this->additional[$key]) {
            unset($this->additional[$key]);
        }
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function getSubtotalGross(): float
    {
        return $this->gross - $this->getCouponGross();
    }

    public function getTotalGross(): float
    {
        return $this->gross - $this->getCouponGross() + $this->getServiceGross();
    }

    public function getSubtotalNet(): float
    {
        return $this->net - $this->getCouponNet();
    }

    public function getTotalNet(): float
    {
        return $this->net - $this->getCouponNet() + $this->getServiceNet();
    }

    public function getIsOrderable(): bool
    {
        return $this->isOrderable();
    }

    public function isOrderable(): bool
    {
        $isOrderable = true;

        if ($this->products) {
            foreach ($this->products as $product) {
                if (!$product->isQuantityInRange()) {
                    $isOrderable = false;
                    break;
                }
            }
        } else {
            $isOrderable = false;
        }

        return $isOrderable;
    }

    public function getBillingCountry(): string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(string $billingCountry): void
    {
        $this->billingCountry = $billingCountry;
    }

    public function isShippingSameAsBilling(): bool
    {
        return $this->shippingSameAsBilling;
    }

    public function setShippingSameAsBilling(bool $shippingSameAsBilling): void
    {
        $this->shippingSameAsBilling = $shippingSameAsBilling;
    }

    public function getShippingCountry(): string
    {
        if ($this->isShippingSameAsBilling()) {
            return $this->getBillingCountry();
        }

        return $this->shippingCountry;
    }

    public function setShippingCountry(string $shippingCountry): void
    {
        $this->shippingCountry = $shippingCountry;
    }

    public function getCountry(): string
    {
        if (!empty($this->shippingCountry)) {
            return $this->shippingCountry;
        }

        return $this->billingCountry;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencyTranslation(): float
    {
        return $this->currencyTranslation;
    }

    public function setCurrencyTranslation(float $currencyTranslation): void
    {
        $this->currencyTranslation = $currencyTranslation;
    }

    public function getCurrencySign(): string
    {
        return $this->currencySign;
    }

    public function setCurrencySign(string $currencySign): void
    {
        $this->currencySign = $currencySign;
    }

    public function translatePrice(float $price = null): ?float
    {
        if ($price !== null) {
            $price /= $this->getCurrencyTranslation();
            return round($price * 100.0) / 100.0;
        }

        return null;
    }
}
