<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\FrontendUser;
use Extcode\Cart\Property\Exception\ResetPropertyException;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Item extends AbstractEntity
{
    protected int $cartPid = 0;

    protected ?FrontendUser $feUser = null;

    protected string $orderNumber;

    protected ?\DateTime $orderDate = null;

    protected string $invoiceNumber;

    protected ?\DateTime $invoiceDate = null;

    protected string $deliveryNumber;

    protected ?\DateTime $deliveryDate = null;

    protected bool $shippingSameAsBilling = false;

    protected ?BillingAddress $billingAddress = null;

    protected ?ShippingAddress $shippingAddress = null;

    protected string $additionalData = '';

    protected string $additional = '';

    protected string $currency = '€';

    protected string $currencyCode = '';

    protected string $currencySign = '';

    protected float $currencyTranslation = 1.00;

    protected float $gross = 0.0;

    protected float $totalGross = 0.0;

    protected float $net = 0.0;

    protected float $totalNet = 0.0;

    /**
     * @Lazy
     * @var ObjectStorage<TaxClass>
     */
    protected ObjectStorage $taxClass;

    /**
     * @var ObjectStorage<Tax>
     */
    protected ObjectStorage $tax;

    /**
     * @var ObjectStorage<Tax>
     */
    protected ObjectStorage $totalTax;

    /**
     * @Lazy
     * @var ObjectStorage<Product>
     */
    protected ObjectStorage $products;

    /**
     * @Lazy
     * @var ObjectStorage<Discount>
     */
    protected ObjectStorage $discounts;

    protected ?Payment $payment = null;

    protected ?Shipping $shipping = null;

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $orderPdfs;

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $invoicePdfs;

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $deliveryPdfs;

    protected \DateTime $crdate;

    protected bool $acceptTermsAndConditions = false;

    protected bool $acceptRevocationInstruction = false;

    protected bool $acceptPrivacyPolicy = false;

    protected string $comment = '';

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->products = new ObjectStorage();
        $this->discounts = new ObjectStorage();
        $this->taxClass = new ObjectStorage();
        $this->tax = new ObjectStorage();
        $this->totalTax = new ObjectStorage();
        $this->orderPdfs = new ObjectStorage();
        $this->invoicePdfs = new ObjectStorage();
        $this->deliveryPdfs = new ObjectStorage();
    }

    public function setCartPid(int $cartPid): void
    {
        $this->cartPid = $cartPid;
    }

    public function getCartPid(): int
    {
        return $this->cartPid;
    }

    public function setFeUser(FrontendUser $feUser): void
    {
        $this->feUser = $feUser;
    }

    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * @throws ResetPropertyException
     */
    public function setOrderNumber(string $orderNumber): string
    {
        if (!isset($this->orderNumber)) {
            $this->orderNumber = $orderNumber;
        } else {
            if ($this->orderNumber !== $orderNumber) {
                throw new ResetPropertyException('Could not reset orderNumber', 1395306283);
            }
        }
        return $this->orderNumber;
    }

    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTime $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    /**
     * @throws ResetPropertyException
     */
    public function setInvoiceNumber(string $invoiceNumber): string
    {
        if (!isset($this->invoiceNumber)) {
            $this->invoiceNumber = $invoiceNumber;
        } else {
            if ($this->invoiceNumber !== $invoiceNumber) {
                throw new ResetPropertyException('Could not reset invoiceNumber', 1395307266);
            }
        }
        return $this->invoiceNumber;
    }

    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTime $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function getDeliveryNumber(): ?string
    {
        return $this->deliveryNumber;
    }

    /**
     * @throws ResetPropertyException
     */
    public function setDeliveryNumber(string $deliveryNumber): string
    {
        if (!isset($this->deliveryNumber)) {
            $this->deliveryNumber = $deliveryNumber;
        } else {
            if ($this->deliveryNumber !== $deliveryNumber) {
                throw new ResetPropertyException('Could not reset deliveryNumber', 1475061197);
            }
        }
        return $this->deliveryNumber;
    }

    public function getDeliveryDate(): ?\DateTime
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTime $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function isShippingSameAsBilling(): bool
    {
        return $this->shippingSameAsBilling;
    }

    public function setShippingSameAsBilling(bool $shippingSameAsBilling): void
    {
        $this->shippingSameAsBilling = $shippingSameAsBilling;
    }

    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(BillingAddress $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function removeShippingAddress(): void
    {
        $this->shippingAddress = null;
    }

    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    public function setAdditionalData(string $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getCurrencySign(): string
    {
        return $this->currencySign;
    }

    public function setCurrencySign(string $currencySign): void
    {
        $this->currencySign = $currencySign;
    }

    public function getCurrencyTranslation(): float
    {
        return $this->currencyTranslation;
    }

    public function setCurrencyTranslation(float $currencyTranslation): void
    {
        $this->currencyTranslation = $currencyTranslation;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function setGross(float $gross): void
    {
        $this->gross = $gross;
    }

    public function getTotalGross(): float
    {
        return $this->totalGross;
    }

    public function setTotalGross(float $totalGross): void
    {
        $this->totalGross = $totalGross;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    public function setNet(float $net): void
    {
        $this->net = $net;
    }

    public function getTotalNet(): float
    {
        return $this->totalNet;
    }

    public function setTotalNet(float $totalNet): void
    {
        $this->totalNet = $totalNet;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    public function unsetPayment(): void
    {
        $this->payment = null;
    }

    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    public function setShipping(Shipping $shipping): void
    {
        $this->shipping = $shipping;
    }

    public function unsetShipping(): void
    {
        $this->shipping = null;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getOrderPdfs(): ObjectStorage
    {
        return $this->orderPdfs;
    }

    /**
     * @param ObjectStorage<FileReference> $orderPdfs
     */
    public function setOrderPdfs(ObjectStorage $orderPdfs): void
    {
        $this->orderPdfs = $orderPdfs;
    }

    public function addOrderPdf(FileReference $orderPdf): void
    {
        $this->orderPdfs->attach($orderPdf);
    }

    public function removeOrderPdf(FileReference $orderPdf): void
    {
        $this->orderPdfs->detach($orderPdf);
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getInvoicePdfs(): ObjectStorage
    {
        return $this->invoicePdfs;
    }

    /**
     * @param ObjectStorage<FileReference> $invoicePdfs
     */
    public function setInvoicePdfs(ObjectStorage $invoicePdfs): void
    {
        $this->invoicePdfs = $invoicePdfs;
    }

    public function addInvoicePdf(FileReference $invoicePdf): void
    {
        $this->invoicePdfs->attach($invoicePdf);
    }

    public function removeInvoicePdf(FileReference $invoicePdf): void
    {
        $this->invoicePdfs->detach($invoicePdf);
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getDeliveryPdfs(): ObjectStorage
    {
        return $this->deliveryPdfs;
    }

    /**
     * @param ObjectStorage<FileReference> $deliveryPdfs
     */
    public function setDeliveryPdfs(ObjectStorage $deliveryPdfs): void
    {
        $this->deliveryPdfs = $deliveryPdfs;
    }

    public function addDeliveryPdf(FileReference $deliveryPdf): void
    {
        $this->deliveryPdfs->attach($deliveryPdf);
    }

    public function removeDeliveryPdf(FileReference $deliveryPdf): void
    {
        $this->deliveryPdfs->detach($deliveryPdf);
    }

    public function addTaxClass(TaxClass $taxClass): void
    {
        $this->taxClass->attach($taxClass);
    }

    public function removeTaxClass(TaxClass $taxClass): void
    {
        $this->taxClass->detach($taxClass);
    }

    /**
     * @return ObjectStorage<TaxClass>
     */
    public function getTaxClass(): ObjectStorage
    {
        return $this->taxClass;
    }

    /**
     * @param ObjectStorage<TaxClass> $taxClass
     */
    public function setTaxClass(ObjectStorage $taxClass): void
    {
        $this->taxClass = $taxClass;
    }

    public function addProduct(Product $product): void
    {
        $this->products->attach($product);
    }

    public function removeProduct(Product $product): void
    {
        $this->products->detach($product);
    }

    /**
     * @return ObjectStorage<Product>
     */
    public function getProducts(): ObjectStorage
    {
        return $this->products;
    }

    /**
     * @param ObjectStorage<Product> $products
     */
    public function setProducts(ObjectStorage $products): void
    {
        $this->products = $products;
    }

    public function addDiscount(Discount $discount): void
    {
        $this->discounts->attach($discount);
    }

    public function removeDiscount(Discount $discount): void
    {
        $this->discounts->detach($discount);
    }

    /**
     * @return ObjectStorage<Discount>
     */
    public function getDiscounts(): ObjectStorage
    {
        return $this->discounts;
    }

    /**
     * @param ObjectStorage<Discount> $discounts
     */
    public function setDiscounts(ObjectStorage $discounts): void
    {
        $this->discounts = $discounts;
    }

    public function addTax(Tax $tax): void
    {
        $this->tax->attach($tax);
    }

    public function removeTax(Tax $tax): void
    {
        $this->tax->detach($tax);
    }

    /**
     * @return ObjectStorage<Tax>
     */
    public function getTax(): ObjectStorage
    {
        return $this->tax;
    }

    /**
     * @param ObjectStorage<Tax> $tax
     */
    public function setTax(ObjectStorage $tax): void
    {
        $this->tax = $tax;
    }

    public function addTotalTax($tax): void
    {
        $this->totalTax->attach($tax);
    }

    public function removeTotalTax($tax): void
    {
        $this->totalTax->detach($tax);
    }

    /**
     * @return ObjectStorage<Tax>
     */
    public function getTotalTax(): ObjectStorage
    {
        return $this->totalTax;
    }

    /**
     * @param ObjectStorage<Tax> $taxes
     */
    public function setTotalTax(ObjectStorage $taxes): void
    {
        $this->totalTax = $taxes;
    }

    public function getCrdate(): ?\DateTime
    {
        return $this->crdate;
    }

    public function setCrdate(\DateTime $crdate): void
    {
        $this->crdate = $crdate;
    }

    public function isAcceptTermsAndConditions(): bool
    {
        return $this->acceptTermsAndConditions;
    }

    public function setAcceptTermsAndConditions(bool $acceptTermsAndConditions): void
    {
        $this->acceptTermsAndConditions = $acceptTermsAndConditions;
    }

    public function isAcceptRevocationInstruction(): bool
    {
        return $this->acceptRevocationInstruction;
    }

    public function setAcceptRevocationInstruction(bool $acceptRevocationInstruction): void
    {
        $this->acceptRevocationInstruction = $acceptRevocationInstruction;
    }

    public function isAcceptPrivacyPolicy(): bool
    {
        return $this->acceptPrivacyPolicy;
    }

    public function setAcceptPrivacyPolicy(bool $acceptPrivacyPolicy): void
    {
        $this->acceptPrivacyPolicy = $acceptPrivacyPolicy;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    public function setAdditional(array $additional): void
    {
        $this->additional = json_encode($additional);
    }
}
