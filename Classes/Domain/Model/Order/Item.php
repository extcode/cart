<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Property\Exception\ResetPropertyException;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Item extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var int
     */
    protected $cartPid = 0;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser = null;

    /**
     * @var string
     */
    protected $orderNumber;

    /**
     * @var \DateTime
     */
    protected $orderDate = null;

    /**
     * @var string
     */
    protected $invoiceNumber;

    /**
     * @var \DateTime
     */
    protected $invoiceDate = null;

    /**
     * @var string
     */
    protected $deliveryNumber;

    /**
     * @var \DateTime
     */
    protected $deliveryDate = null;

    /**
     * @var bool
     */
    protected $shippingSameAsBilling = false;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\BillingAddress
     */
    protected $billingAddress;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\ShippingAddress
     */
    protected $shippingAddress;

    /**
     * @var string
     */
    protected $additionalData = '';

    /**
     * @var string
     */
    protected $additional = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $currency = '€';

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
    protected $currencyTranslation = 1.00;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $gross = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $totalGross = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $net = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $totalNet = 0.0;

    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\TaxClass>
     */
    protected $taxClass;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax>
     */
    protected $tax;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax>
     */
    protected $totalTax;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Product>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $products;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Discount>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $discounts;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\Shipping
     */
    protected $shipping;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $orderPdfs;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $invoicePdfs;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $deliveryPdfs;

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var bool
     */
    protected $acceptTermsAndConditions = false;

    /**
     * @var bool
     */
    protected $acceptRevocationInstruction = false;

    /**
     * @var bool
     */
    protected $acceptPrivacyPolicy = false;

    /**
     * @var string
     */
    protected $comment = '';

    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorages.
     */
    protected function initStorageObjects()
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

    /**
     * @var int $cartPid
     */
    public function setCartPid(int $cartPid)
    {
        $this->cartPid = $cartPid;
    }

    /**
     * @return int
     */
    public function getCartPid(): int
    {
        return $this->cartPid;
    }

    /**
     * @param FrontendUser $feUser
     */
    public function setFeUser(FrontendUser $feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * @return FrontendUser|null
     */
    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    /**
     * @return string|null
     */
    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     * @return string
     *
     * @throws ResetPropertyException
     */
    public function setOrderNumber(string $orderNumber): string
    {
        if (!$this->orderNumber) {
            $this->orderNumber = $orderNumber;
        } else {
            if ($this->orderNumber != $orderNumber) {
                throw new ResetPropertyException('Could not reset orderNumber', 1395306283);
            }
        }
        return $this->orderNumber;
    }

    /**
     * @return \DateTime|null
     */
    public function getOrderDate(): ?\DateTime
    {
        return $this->orderDate;
    }

    /**
     * @param \DateTime $orderDate
     */
    public function setOrderDate(\DateTime $orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * @return string|null
     */
    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     *
     * @return string
     * @throws ResetPropertyException
     */
    public function setInvoiceNumber(string $invoiceNumber): string
    {
        if (!$this->invoiceNumber) {
            $this->invoiceNumber = $invoiceNumber;
        } else {
            if ($this->invoiceNumber != $invoiceNumber) {
                throw new ResetPropertyException('Could not reset invoiceNumber', 1395307266);
            }
        }
        return $this->invoiceNumber;
    }

    /**
     * @return \DateTime|null
     */
    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    /**
     * @param \DateTime $invoiceDate
     */
    public function setInvoiceDate(\DateTime $invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
    }

    /**
     * @return string|null
     */
    public function getDeliveryNumber(): ?string
    {
        return $this->deliveryNumber;
    }

    /**
     * @param string $deliveryNumber
     *
     * @return string
     * @throws ResetPropertyException
     */
    public function setDeliveryNumber(string $deliveryNumber): string
    {
        if (!$this->deliveryNumber) {
            $this->deliveryNumber = $deliveryNumber;
        } else {
            if ($this->deliveryNumber != $deliveryNumber) {
                throw new ResetPropertyException('Could not reset deliveryNumber', 1475061197);
            }
        }
        return $this->deliveryNumber;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeliveryDate(): ?\DateTime
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate(\DateTime $deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
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
     * @return BillingAddress|null
     */
    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billingAddress;
    }

    /**
     * @param BillingAddress $billingAddress
     */
    public function setBillingAddress(BillingAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return ShippingAddress|null
     */
    public function getShippingAddress(): ?ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @param ShippingAddress $shippingAddress
     */
    public function setShippingAddress(ShippingAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * Remove Shopping Address
     */
    public function removeShippingAddress()
    {
        $this->shippingAddress = null;
    }

    /**
     * @return string
     */
    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     */
    public function setAdditionalData(string $additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
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
     * @return float $gross
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @param float $gross
     */
    public function setGross(float $gross)
    {
        $this->gross = $gross;
    }

    /**
     * @return float $totalGross
     */
    public function getTotalGross(): float
    {
        return $this->totalGross;
    }

    /**
     * @param float $totalGross
     */
    public function setTotalGross(float $totalGross)
    {
        $this->totalGross = $totalGross;
    }

    /**
     * @return float $net
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @param float $net
     */
    public function setNet(float $net)
    {
        $this->net = $net;
    }

    /**
     * @return float $totalNet
     */
    public function getTotalNet(): float
    {
        return $this->totalNet;
    }

    /**
     * @param float $totalNet
     */
    public function setTotalNet(float $totalNet)
    {
        $this->totalNet = $totalNet;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @param Shipping $shipping
     */
    public function setShipping(Shipping $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return Shipping|null
     */
    public function getShipping(): ?Shipping
    {
        return $this->shipping;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getOrderPdfs()
    {
        return $this->orderPdfs;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $orderPdfs
     */
    public function setOrderPdfs($orderPdfs)
    {
        $this->orderPdfs = $orderPdfs;
    }

    /**
     * @param FileReference $orderPdf
     */
    public function addOrderPdf(FileReference $orderPdf)
    {
        $this->orderPdfs->attach($orderPdf);
    }

    /**
     * @param FileReference $orderPdf
     */
    public function removeOrderPdf(FileReference $orderPdf)
    {
        $this->orderPdfs->detach($orderPdf);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getInvoicePdfs()
    {
        return $this->invoicePdfs;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $invoicePdf
     */
    public function setInvoicePdfs($invoicePdfs)
    {
        $this->invoicePdfs = $invoicePdfs;
    }

    /**
     * @param FileReference $invoicePdf
     */
    public function addInvoicePdf(FileReference $invoicePdf)
    {
        $this->invoicePdfs->attach($invoicePdf);
    }

    /**
     * @param FileReference $invoicePdf
     */
    public function removeInvoicePdf(FileReference $invoicePdf)
    {
        $this->invoicePdfs->detach($invoicePdf);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getDeliveryPdfs()
    {
        return $this->deliveryPdfs;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $deliveryPdfs
     */
    public function setDeliveryPdfs($deliveryPdfs)
    {
        $this->deliveryPdfs = $deliveryPdfs;
    }

    /**
     * @param FileReference $deliveryPdf
     */
    public function addDeliveryPdf(FileReference $deliveryPdf)
    {
        $this->deliveryPdfs->attach($deliveryPdf);
    }

    /**
     * @param FileReference $deliveryPdf
     */
    public function removeDeliveryPdf(FileReference $deliveryPdf)
    {
        $this->deliveryPdfs->detach($deliveryPdf);
    }

    /**
     * @param TaxClass $taxClass
     */
    public function addTaxClass(TaxClass $taxClass)
    {
        $this->taxClass->attach($taxClass);
    }

    /**
     * @param TaxClass $taxClass
     */
    public function removeTaxClass(TaxClass $taxClass)
    {
        $this->taxClass->detach($taxClass);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\TaxClass>
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\TaxClass> $taxClass
     */
    public function setTaxClass($taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * @param Product $product
     */
    public function addProduct(Product $product)
    {
        $this->products->attach($product);
    }

    /**
     * @param Product $product
     */
    public function removeProduct(Product $product)
    {
        $this->products->detach($product);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Product>
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Product> $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @param Discount $discount
     */
    public function addDiscount(Discount $discount)
    {
        $this->discounts->attach($discount);
    }

    /**
     * @param Discount $discount
     */
    public function removeDiscount(Discount $discount)
    {
        $this->discounts->detach($discount);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Discount>
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Discount> $discounts
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
    }

    /**
     * @param Tax $tax
     */
    public function addTax(Tax $tax)
    {
        $this->tax->attach($tax);
    }

    /**
     * @param Tax $tax
     */
    public function removeTax(Tax $tax)
    {
        $this->tax->detach($tax);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax>
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax> $taxes
     */
    public function setTax($taxes)
    {
        $this->tax = $taxes;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Tax $tax
     */
    public function addTotalTax($tax)
    {
        $this->totalTax->attach($tax);
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Tax $tax
     */
    public function removeTotalTax($tax)
    {
        $this->totalTax->detach($tax);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax>
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax> $taxes
     */
    public function setTotalTax($taxes)
    {
        $this->totalTax = $taxes;
    }

    /**
     * @return \DateTime|null
     */
    public function getCrdate(): ?\DateTime
    {
        return $this->crdate;
    }

    /**
     * @param \DateTime $crdate
     */
    public function setCrdate(\DateTime $crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return bool
     */
    public function isAcceptTermsAndConditions(): bool
    {
        return $this->acceptTermsAndConditions;
    }

    /**
     * @param bool $acceptTermsAndConditions
     */
    public function setAcceptTermsAndConditions(bool $acceptTermsAndConditions)
    {
        $this->acceptTermsAndConditions = $acceptTermsAndConditions;
    }

    /**
     * @return bool
     */
    public function isAcceptRevocationInstruction(): bool
    {
        return $this->acceptRevocationInstruction;
    }

    /**
     * @param bool $acceptRevocationInstruction
     */
    public function setAcceptRevocationInstruction(bool $acceptRevocationInstruction)
    {
        $this->acceptRevocationInstruction = $acceptRevocationInstruction;
    }

    /**
     * @return bool
     */
    public function isAcceptPrivacyPolicy(): bool
    {
        return $this->acceptPrivacyPolicy;
    }

    /**
     * @param bool $acceptPrivacyPolicy
     */
    public function setAcceptPrivacyPolicy(bool $acceptPrivacyPolicy)
    {
        $this->acceptPrivacyPolicy = $acceptPrivacyPolicy;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    /**
     * @param array $additional
     */
    public function setAdditional(array $additional)
    {
        $this->additional = json_encode($additional);
    }
}
