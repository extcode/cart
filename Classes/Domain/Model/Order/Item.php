<?php

namespace Extcode\Cart\Domain\Model\Order;

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
use Extcode\Cart\Property\Exception\ResetPropertyException;

/**
 * Order Item Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Item extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Cart Pid
     *
     * @var int
     */
    protected $cartPid = 0;

    /**
     * FeUser
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feUser = null;

    /**
     * Order Number
     *
     * @var string
     */
    protected $orderNumber;

    /**
     * Order Date
     *
     * @var \DateTime
     */
    protected $orderDate = null;

    /**
     * Invoice Number
     *
     * @var string
     */
    protected $invoiceNumber;

    /**
     * Invoice Date
     *
     * @var \DateTime
     */
    protected $invoiceDate = null;

    /**
     * Delivery Number
     *
     * @var string
     */
    protected $deliveryNumber;

    /**
     * Delivery Date
     *
     * @var \DateTime
     */
    protected $deliveryDate = null;

    /**
     * Billing Address
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \Extcode\Cart\Domain\Model\Order\Address
     */
    protected $billingAddress;

    /**
     * Shipping Address
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \Extcode\Cart\Domain\Model\Order\Address
     */
    protected $shippingAddress;

    /**
     * Additional Data
     *
     * @var string
     */
    protected $additionalData;

    /**
     * Additional
     *
     * @var string
     */
    protected $additional;

    /**
     * Currency
     *
     * @var string
     * @validate NotEmpty
     */
    protected $currency = '€';

    /**
     * Currency Code
     *
     * @var string
     */
    protected $currencyCode = '';

    /**
     * Currency Sign
     *
     * @var string
     */
    protected $currencySign = '';

    /**
     * Currency Translation
     *
     * @var float
     */
    protected $currencyTranslation = 1.00;

    /**
     * Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $gross = 0.0;

    /**
     * Total Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $totalGross = 0.0;

    /**
     * Net
     *
     * @var float
     * @validate NotEmpty
     */
    protected $net = 0.0;

    /**
     * Total Net
     *
     * @var float
     * @validate NotEmpty
     */
    protected $totalNet = 0.0;

    /**
     * TaxClass
     *
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\TaxClass>
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\Tax>
     */
    protected $tax;

    /**
     * TotalTax
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\Tax>
     */
    protected $totalTax;

    /**
     * Products
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\Product>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $products;

    /**
     * Discounts
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\Discount>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $discounts;

    /**
     * Payment
     *
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment;

    /**
     * Shipping
     *
     * @var \Extcode\Cart\Domain\Model\Order\Shipping
     */
    protected $shipping;

    /**
     * Order Pdf
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $orderPdfs;

    /**
     * Invoice Pdf
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $invoicePdfs;

    /**
     * Delivery Pdf
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $deliveryPdfs;

    /**
     * crdate
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * Accept Terms
     *
     * @var bool
     */
    protected $acceptTerms = false;

    /**
     * Accept Conditions
     *
     * @var bool
     */
    protected $acceptConditions = false;

    /**
     * Comment
     *
     * @var string
     */
    protected $comment;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->products = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->discounts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->taxClass = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->tax = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->totalTax = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->orderPdfs = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->invoicePdfs = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->deliveryPdfs = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Set Cart Pid
     *
     * @var int $cartPid
     */
    public function setCartPid($cartPid)
    {
        $this->cartPid = $cartPid;
    }

    /**
     * Get Cart Pid
     *
     * @return int
     */
    public function getCartPid()
    {
        return $this->cartPid;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Returns the orderNumber
     *
     * @return string $orderNumber
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * Sets the orderNumber
     *
     * @param string $orderNumber
     * @return string
     *
     * @throws ResetPropertyException
     */
    public function setOrderNumber($orderNumber)
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
     * Gets Order Date
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Sets Order Date
     *
     * @param \DateTime $orderDate
     */
    public function setOrderDate(\DateTime $orderDate)
    {
        $this->orderDate = $orderDate;
    }

    /**
     * Returns the invoiceNumber
     *
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Sets the invoiceNumber
     *
     * @param string $invoiceNumber
     *
     * @return string
     * @throws ResetPropertyException
     */
    public function setInvoiceNumber($invoiceNumber)
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
     * Gets Invoice Date
     *
     * @return \DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * Sets Invoice Date
     *
     * @param \DateTime $invoiceDate
     */
    public function setInvoiceDate(\DateTime $invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
    }

    /**
     * Returns the deliveryNumber
     *
     * @return string
     */
    public function getDeliveryNumber()
    {
        return $this->deliveryNumber;
    }

    /**
     * Sets the deliveryNumber
     *
     * @param string $deliveryNumber
     *
     * @return string
     * @throws ResetPropertyException
     */
    public function setDeliveryNumber($deliveryNumber)
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
     * Gets Delivery Date
     *
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * Sets Delivery Date
     *
     * @param \DateTime $deliveryDate
     */
    public function setDeliveryDate(\DateTime $deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    /**
     * Gets Billing Address
     *
     * @return \Extcode\Cart\Domain\Model\Order\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set Billing Address
     *
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     */
    public function setBillingAddress(\Extcode\Cart\Domain\Model\Order\Address $billingAddress)
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * Gets Shipping Address
     *
     * @return \Extcode\Cart\Domain\Model\Order\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set Shopping Address
     *
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     */
    public function setShippingAddress(\Extcode\Cart\Domain\Model\Order\Address $shippingAddress)
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
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * Returns Currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Sets Currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Returns Currency Code
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Sets Currency Code
     *
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * Returns Currency Sign
     *
     * @return string
     */
    public function getCurrencySign()
    {
        return $this->currencySign;
    }

    /**
     * Sets Currency Sign
     *
     * @param string $currencySign
     */
    public function setCurrencySign($currencySign)
    {
        $this->currencySign = $currencySign;
    }

    /**
     * Returns Currency Translation
     *
     * @return float
     */
    public function getCurrencyTranslation()
    {
        return $this->currencyTranslation;
    }

    /**
     * Set Currency Translation
     *
     * @param float $currencyTranslation
     */
    public function setCurrencyTranslation($currencyTranslation)
    {
        $this->currencyTranslation = $currencyTranslation;
    }

    /**
     * Returns Gross
     *
     * @return float $gross
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * Sets Gross
     *
     * @param float $gross
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    /**
     * Returns Total Gross
     *
     * @return float $totalGross
     */
    public function getTotalGross()
    {
        return $this->totalGross;
    }

    /**
     * Sets Total Gross
     *
     * @param float $totalGross
     */
    public function setTotalGross($totalGross)
    {
        $this->totalGross = $totalGross;
    }

    /**
     * Returns Met
     *
     * @return float $net
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * Sets Net
     *
     * @param float $net
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * Returns Total Net
     *
     * @return float $totalNet
     */
    public function getTotalNet()
    {
        return $this->totalNet;
    }

    /**
     * Sets Total Net
     *
     * @param float $totalNet
     */
    public function setTotalNet($totalNet)
    {
        $this->totalNet = $totalNet;
    }

    /**
     * Sets Payment
     *
     * @param \Extcode\Cart\Domain\Model\Order\Payment $payment
     */
    public function setPayment(\Extcode\Cart\Domain\Model\Order\Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Gets Payment
     *
     * @return \Extcode\Cart\Domain\Model\Order\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Sets Shipping
     *
     * @param \Extcode\Cart\Domain\Model\Order\Shipping $shipping
     */
    public function setShipping(\Extcode\Cart\Domain\Model\Order\Shipping $shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * Gets Shipping
     *
     * @return \Extcode\Cart\Domain\Model\Order\Shipping
     */
    public function getShipping()
    {
        return $this->shipping;
    }

    /**
     * Returns Order PDF
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getOrderPdfs()
    {
        return $this->orderPdfs;
    }

    /**
     * Sets Order PDF
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function setOrderPdfs($orderPdfs)
    {
        $this->orderPdfs = $orderPdfs;
    }

    /**
     * Adds a Order PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $orderPdf
     */
    public function addOrderPdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $orderPdf)
    {
        $this->orderPdfs->attach($orderPdf);
    }

    /**
     * Removes a Order PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $orderPdfToRemove
     */
    public function removeOrderPdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $orderPdfToRemove)
    {
        $this->orderPdfs->detach($orderPdfToRemove);
    }

    /**
     * Returns Invoice PDFs
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getInvoicePdfs()
    {
        return $this->invoicePdfs;
    }

    /**
     * Sets Invoice PDFs
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference $invoicePdf>
     */
    public function setInvoicePdfs($invoicePdfs)
    {
        $this->invoicePdfs = $invoicePdfs;
    }

    /**
     * Adds a Invoice PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $invoicePdf
     */
    public function addInvoicePdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $invoicePdf)
    {
        $this->invoicePdfs->attach($invoicePdf);
    }

    /**
     * Removes a Invoice PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $invoicePdfToRemove
     */
    public function removeInvoicePdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $invoicePdfToRemove)
    {
        $this->invoicePdfs->detach($invoicePdfToRemove);
    }

    /**
     * Returns Delivery PDFs
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getDeliveryPdfs()
    {
        return $this->deliveryPdfs;
    }

    /**
     * Sets Delivery PDFs
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference $deliveryPdf>
     */
    public function setDeliveryPdfs($deliveryPdfs)
    {
        $this->deliveryPdfs = $deliveryPdfs;
    }

    /**
     * Adds a Delivery PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $deliveryPdf
     */
    public function addDeliveryPdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $deliveryPdf)
    {
        $this->deliveryPdfs->attach($deliveryPdf);
    }

    /**
     * Removes a Delivery PDF
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $deliveryPdfToRemove
     */
    public function removeDeliveryPdf(\TYPO3\CMS\Extbase\Domain\Model\FileReference $deliveryPdfToRemove)
    {
        $this->deliveryPdfs->detach($deliveryPdfToRemove);
    }

    /**
     * Adds a TaxClass
     *
     * @param \Extcode\Cart\Domain\Model\Order\TaxClass $taxClass
     */
    public function addTaxClass(\Extcode\Cart\Domain\Model\Order\TaxClass $taxClass)
    {
        $this->taxClass->attach($taxClass);
    }

    /**
     * Removes a OrderTaxClass
     *
     * @param \Extcode\Cart\Domain\Model\Order\TaxClass $taxClassToRemove
     */
    public function removeTaxClass(\Extcode\Cart\Domain\Model\Order\TaxClass $taxClassToRemove)
    {
        $this->taxClass->detach($taxClassToRemove);
    }

    /**
     * Returns TaxClass
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\TaxClass> $taxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Sets TaxClass
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage <\Extcode\Cart\Domain\Model\Order\TaxClass> $taxClass
     */
    public function setTaxClass($taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * Adds a Product
     *
     * @param \Extcode\Cart\Domain\Model\Order\Product $product
     */
    public function addProduct(\Extcode\Cart\Domain\Model\Order\Product $product)
    {
        $this->products->attach($product);
    }

    /**
     * Removes a Product
     *
     * @param \Extcode\Cart\Domain\Model\Order\Product $productToRemove
     */
    public function removeProduct(\Extcode\Cart\Domain\Model\Order\Product $productToRemove)
    {
        $this->products->detach($productToRemove);
    }

    /**
     * Returns product
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Product> $products
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Sets Product
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Product> $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * Adds a Discount
     *
     * @param \Extcode\Cart\Domain\Model\Order\Discount $discount
     */
    public function addDiscount(\Extcode\Cart\Domain\Model\Order\Discount $discount)
    {
        $this->discounts->attach($discount);
    }

    /**
     * Removes a Discount
     *
     * @param \Extcode\Cart\Domain\Model\Order\Discount $discountToRemove
     */
    public function removeDiscount(\Extcode\Cart\Domain\Model\Order\Discount $discountToRemove)
    {
        $this->discounts->detach($discountToRemove);
    }

    /**
     * Returns Discounts
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Discount> $discount
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Sets Discounts
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Discount> $discounts
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
    }

    /**
     * Adds a Tax
     *
     * @param \Extcode\Cart\Domain\Model\Order\Tax $tax
     */
    public function addTax($tax)
    {
        $this->tax->attach($tax);
    }

    /**
     * Removes a Tax
     *
     * @param \Extcode\Cart\Domain\Model\Order\Tax $taxToRemove
     */
    public function removeTax($taxToRemove)
    {
        $this->tax->detach($taxToRemove);
    }

    /**
     * Returns the Tax
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\OrderTax>
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Sets the Tax
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax> $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * Adds a TotalTax
     *
     * @param \Extcode\Cart\Domain\Model\Order\Tax $totalTax
     */
    public function addTotalTax($totalTax)
    {
        $this->totalTax->attach($totalTax);
    }

    /**
     * Removes a TotalTax
     *
     * @param \Extcode\Cart\Domain\Model\Order\Tax $totalTaxToRemove
     */
    public function removeTotalTax($totalTaxToRemove)
    {
        $this->totalTax->detach($totalTaxToRemove);
    }

    /**
     * Returns the TotalTax
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax> $totalTax
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * Sets the TotalTax
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Tax> $totalTax
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;
    }

    /**
     * Returns the crdate
     *
     * @return \DateTime $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate
     *
     * @param \DateTime $crdate
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * @return bool
     */
    public function getAcceptConditions()
    {
        return $this->acceptConditions;
    }

    /**
     * @param bool $acceptConditions
     */
    public function setAcceptConditions($acceptConditions)
    {
        $this->acceptConditions = $acceptConditions;
    }

    /**
     * @return bool
     */
    public function getAcceptTerms()
    {
        return $this->acceptTerms;
    }

    /**
     * @param bool $acceptTerms
     */
    public function setAcceptTerms($acceptTerms)
    {
        $this->acceptTerms = $acceptTerms;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function getAdditional()
    {
        return json_decode($this->additional, 1);
    }

    /**
     * @return string
     */
    public function getAdditionalJson()
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        $this->additional = json_encode($additional);
    }
}
