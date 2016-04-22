<?php

namespace Extcode\Cart\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Order Utility
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class OrderUtility
{
    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     * @inject
     */
    protected $orderItemRepository;

    /**
     * Coupon Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\CouponRepository
     * @inject
     */
    protected $couponRepository;

    /**
     * Product Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ProductRepository
     * @inject
     */
    protected $productRepository;

    /**
     * Product Additional Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ProductAdditionalRepository
     * @inject
     */
    protected $productAdditionalRepository;

    /**
     * Address Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\AddressRepository
     * @inject
     */
    protected $addressRepository;

    /**
     * Payment Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\PaymentRepository
     * @inject
     */
    protected $paymentRepository;

    /**
     * Shipping Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ShippingRepository
     * @inject
     */
    protected $shippingRepository;

    /**
     * Tax Class Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\TaxClassRepository
     * @inject
     */
    protected $taxClassRepository;

    /**
     * Order Tax Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\TaxRepository
     * @inject
     */
    protected $taxRepository;

    /**
     * Product Product Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Product\ProductRepository
     * @inject
     */
    protected $productProductRepository;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Tax Classes
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\TaxClass>
     */
    private $taxClasses;

    /**
     * Order Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $orderItem;

    /**
     * Storage Pid
     *
     * @var int
     */
    protected $storagePid = null;

    /**
     * Save Order
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     *
     * @return void
     */
    public function saveOrderItem(
        array $pluginSettings,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart,
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->storagePid = $pluginSettings['settings']['order']['pid'];

        $this->cart = $cart;
        $this->orderItem = $orderItem;

        if (!$this->objectManager) {
            $this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        }

        $orderItem->setPid($this->storagePid);

        $orderItem->setFeUser((int) $GLOBALS['TSFE']->fe_user->user['uid']);

        $orderItem->setGross($this->cart->getGross());
        $orderItem->setNet($this->cart->getNet());
        $orderItem->setTotalGross($this->cart->getTotalGross());
        $orderItem->setTotalNet($this->cart->getTotalNet());

        $billingAddress->setPid($this->storagePid);
        $orderItem->setBillingAddress($billingAddress);
        if ($shippingAddress && !$shippingAddress->_isDirty()) {
            $shippingAddress->setPid($this->storagePid);
            $orderItem->setShippingAddress($shippingAddress);
        }

        if (!$orderItem->_isDirty()) {
            $this->orderItemRepository->add($orderItem);

            $this->addTaxClasses();

            $this->addTaxes('TotalTax');
            $this->addTaxes('Tax');

            if ($this->cart->getProducts()) {
                $this->addProducts();
            }
            if ($this->cart->getCoupons()) {
                $this->addCoupons();
            }
            if ($this->cart->getPayment()) {
                $this->addPayment();
            }
            if ($this->cart->getShipping()) {
                $this->addShipping();
            }
        }

        $orderNumber = $this->getOrderNumber($pluginSettings);

        $orderItem->setOrderNumber($orderNumber);
        $orderItem->setOrderDate(new \DateTime());

        $this->persistenceManager->persistAll();

        $this->cart->setOrderId($orderItem->getUid());
        $this->cart->setOrderNumber($orderItem->getOrderNumber());
    }

    /**
     * Check Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function checkStock(\Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        // TODO internal stock check

        $data = array(
            'cart' => $cart,
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            'afterInternalCheckStock',
            array($data)
        );
    }

    /**
     * Handle Stock
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function handleStock(\Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        $data = array(
            'cart' => $cart,
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            'beforeHandleStock',
            array($data)
        );

        foreach ($cart->getProducts() as $cartProduct) {
            /** @var $cartProduct \Extcode\Cart\Domain\Model\Cart\Product */
            if (!$cartProduct->getContentId()) {
                $productProduct = $this->productProductRepository->findByUid($cartProduct->getProductId());
                if ($productProduct) {
                    $productProduct->removeFromStock($cartProduct->getQuantity());
                }
                $this->productProductRepository->update($productProduct);
            }
        }

        $this->persistenceManager->persistAll();

        $data = array(
            'cart' => $cart,
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            'afterHandleStock',
            array($data)
        );
    }

    /**
     * Handle Payment
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function handlePayment(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $payment = $cart->getPayment();
        $provider = $payment->getAdditional('payment_service');

        $data = array(
            'orderItem' => $orderItem,
            'cart' => $cart,
            'provider' => $provider
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__ .
            'AfterOrder',
            array($data)
        );
    }

    /**
     * Adds a Taxes To Order
     *
     * @param string $type Type of the Tax
     *
     * @return void
     */
    protected function addTaxes($type = 'Tax')
    {
        $cartTaxes = call_user_func(array($this->cart, 'get' . $type . 'es'));
        foreach ($cartTaxes as $cartTaxKey => $cartTax) {
            /**
             * Order Tax
             * @var $orderTax \Extcode\Cart\Domain\Model\Order\Tax
             */
            $orderTax = new \Extcode\Cart\Domain\Model\Order\Tax(
                $cartTax,
                $this->taxClasses[$cartTaxKey]
            );
            $orderTax->setPid($this->storagePid);

            $this->taxRepository->add($orderTax);

            call_user_func(array($this->orderItem, 'add' . $type), $orderTax);
        }
    }

    /**
     * Add TaxClasses to Order Item
     *
     * @return void
     */
    protected function addTaxClasses()
    {
        foreach ($this->cart->getTaxClasses() as $taxClass) {
            /**
             * @var \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
             */
            /**
             * @var \Extcode\Cart\Domain\Model\Order\TaxClass $orderTaxClass
             */
            $orderTaxClass = new \Extcode\Cart\Domain\Model\Order\TaxClass(
                $taxClass->getTitle(),
                $taxClass->getValue(),
                $taxClass->getCalc()
            );
            $orderTaxClass->setPid($this->storagePid);

            $this->taxClassRepository->add($orderTaxClass);

            $this->orderItem->addTaxClass($orderTaxClass);

            $this->taxClasses[$taxClass->getId()] = $orderTaxClass;
        }
    }

    /**
     * Add Coupons to Order Item
     */
    protected function addCoupons()
    {
        /**
         * @var $cartCoupon \Extcode\Cart\Domain\Model\Cart\CartCoupon
         */
        foreach ($this->cart->getCoupons() as $cartCoupon) {
            if ($cartCoupon->getIsUseable()) {
                $orderCoupon = new \Extcode\Cart\Domain\Model\Order\Coupon(
                    $cartCoupon->getTitle(),
                    $cartCoupon->getCode(),
                    $cartCoupon->getDiscount(),
                    $cartCoupon->getTaxClass(),
                    $cartCoupon->getTax()
                );
                $orderCoupon->setPid($this->storagePid);

                $this->couponRepository->add($orderCoupon);

                $this->orderItem->addCoupon($orderCoupon);
            }
        }
    }

    /**
     * Add Products to Order Item
     *
     * @return void
     */
    protected function addProducts()
    {
        /**
         * @var $cartProduct \Extcode\Cart\Domain\Model\Cart\Product
         */
        foreach ($this->cart->getProducts() as $cartProduct) {
            if ($cartProduct->getBeVariants()) {
                $this->addProductVariants($cartProduct);
            } else {
                $this->addProduct($cartProduct);
            }
        }
    }

    /**
     * Add CartProduct to Order Item
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $cartProduct
     *
     * @return void
     */
    protected function addProduct(\Extcode\Cart\Domain\Model\Cart\Product $cartProduct)
    {
        /**
         * @var \Extcode\Cart\Domain\Model\Order\Product $orderProduct
         */
        $orderProduct = new \Extcode\Cart\Domain\Model\Order\Product(
            $cartProduct->getSku(),
            $cartProduct->getTitle(),
            $cartProduct->getQuantity()
        );
        $orderProduct->setPid($this->storagePid);

        $orderProduct->setPrice($cartProduct->getPrice());
        $orderProduct->setGross($cartProduct->getGross());
        $orderProduct->setNet($cartProduct->getNet());
        $orderProduct->setTaxClass($this->taxClasses[$cartProduct->getTaxClass()->getId()]);
        $orderProduct->setTax($cartProduct->getTax());

        $additionalArray = $cartProduct->getAdditionalArray();

        $data = array(
            'cartProduct' => $cartProduct,
            'orderProduct' => &$orderProduct,
            'additionalArray' => &$additionalArray,
            'storagePid' => $this->storagePid,
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__ .
            'BeforeSetAdditionalData',
            array($data)
        );

        $orderProduct->setAdditionalData(json_encode($data['additionalArray']));

        $this->productRepository->add($orderProduct);

        $this->orderItem->addProduct($orderProduct);

        $this->addFeVariants($orderProduct, $cartProduct->getFeVariant());
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\FeVariant $feVariant
     */
    protected function addFeVariants(
        \Extcode\Cart\Domain\Model\Order\Product $product,
        \Extcode\Cart\Domain\Model\Cart\FeVariant $feVariant = null
    ) {
        if ($feVariant) {
            $feVariantsData = $feVariant->getVariantData();
            if ($feVariantsData) {
                foreach ($feVariantsData as $feVariant) {
                    $this->addProductAdditional('FeVariant', $product, $feVariant);
                }
            }
        }
    }

    /**
     * @param string $type
     * @param \Extcode\Cart\Domain\Model\Order\Product $product
     * @param array $feVariant
     */
    protected function addProductAdditional(
        $productAdditionalType,
        \Extcode\Cart\Domain\Model\Order\Product $product,
        $feVariant
    ) {
        /**
         * @var \Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional
         */
        $productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $productAdditionalType,
            $feVariant['sku'],
            $feVariant['value'],
            $feVariant['title']
        );
        $productAdditional->setPid($this->storagePid);

        $this->productAdditionalRepository->add($productAdditional);

        $product->addProductAdditional($productAdditional);
    }


    /**
     * Adds Variants of a CartProduct to Order Item
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product CartProduct
     *
     * @return void
     */
    protected function addProductVariants(\Extcode\Cart\Domain\Model\Cart\Product $product)
    {
        foreach ($product->getBeVariants() as $variant) {
            /**
             * Cart Variant
             * @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
             */
            if ($variant->getBeVariants()) {
                $this->addVariantsOfVariant($variant, 1);
            } else {
                $this->addBeVariant($variant, 1);
            }
        }
    }

    /**
     * Adds Variants of a Variant to Order Item
     *
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param int $level Level
     *
     * @return void
     */
    protected function addVariantsOfVariant(\Extcode\Cart\Domain\Model\Cart\BeVariant $variant, $level)
    {
        $level += 1;

        foreach ($variant->getBeVariants() as $variantInner) {
            /**
             * Cart Variant Inner
             * @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variantInner
             */
            if ($variantInner->getBeVariants()) {
                $this->addVariantsOfVariant($variantInner, $level);
            } else {
                $this->addBeVariant($variantInner, $level);
            }
        }
    }

    /**
     * Adds a Variant to Order Item
     *
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param int $level Level
     *
     * @return void
     */
    protected function addBeVariant(\Extcode\Cart\Domain\Model\Cart\BeVariant $variant, $level)
    {
        /** @var \Extcode\Cart\Domain\Model\Order\Tax $orderTax */
        $orderTax = new \Extcode\Cart\Domain\Model\Order\Tax(
            $variant->getTax(),
            $this->taxClasses[$variant->getTaxClass()->getId()]
        );
        $orderTax->setPid($this->storagePid);

        $this->taxRepository->add($orderTax);

        /**
         * Order Product
         * @var \Extcode\Cart\Domain\Model\Order\Product $orderProduct
         */
        $orderProduct = new \Extcode\Cart\Domain\Model\Order\Product(
            $variant->getSku(),
            $variant->getTitle(),
            $variant->getQuantity()
        );
        $orderProduct->setPid($this->storagePid);

        $skuWithVariants = array();
        $titleWithVariants = array();

        $variantInner = $variant;
        for ($count = $level; $count > 0; $count--) {
            $skuWithVariants['variantsku' . $count] = $variantInner->getSku();
            $titleWithVariants['varianttitle' . $count] = $variantInner->getTitle();

            if ($count > 1) {
                $variantInner = $variantInner->getParentBeVariant();
            } else {
                $cartProduct = $variantInner->getProduct();
            }
        }
        unset($variantInner);

        $skuWithVariants['sku'] = $cartProduct->getSku();
        $titleWithVariants['title'] = $cartProduct->getTitle();

        $orderProduct->setPrice($variant->getPrice());
        $orderProduct->setDiscount($variant->getDiscount());
        $orderProduct->setGross($variant->getGross());
        $orderProduct->setNet($variant->getNet());
        $orderProduct->setTaxClass($this->taxClasses[$variant->getTaxClass()->getId()]);
        $orderProduct->setTax($variant->getTax());

        if (!$orderProduct->_isDirty()) {
            $this->productRepository->add($orderProduct);
        }

        $this->addFeVariants($orderProduct, $cartProduct->getFeVariant());

        $variantInner = $variant;
        for ($count = $level; $count > 0; $count--) {
            /**
             * @var \Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional
             */
            $orderProductAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
                'variant_' . $count,
                $variantInner->getSku(),
                $variantInner->getTitle()
            );
            $orderProductAdditional->setPid($this->storagePid);

            $this->productAdditionalRepository->add($orderProductAdditional);

            $orderProduct->addProductAdditional($orderProductAdditional);

            if ($count > 1) {
                $variantInner = $variantInner->getParentBeVariant();
            } else {
                $cartProduct = $variantInner->getProduct();
            }
        }
        unset($variantInner);

        $additionalArray = $cartProduct->getAdditionalArray();

        $data = array(
            'cartProduct' => $cartProduct,
            'orderProduct' => &$orderProduct,
            'additionalArray' => &$additionalArray,
            'storagePid' => $this->storagePid,
        );

        $signalSlotDispatcher = $this->objectManager->get('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__ .
            'BeforeSetAdditionalData',
            array($data)
        );

        $orderProduct->setAdditionalData(json_encode($data['additionalArray']));

        $this->productRepository->add($orderProduct);

        $this->orderItem->addProduct($orderProduct);
    }

    /**
     * Add Billing Address
     *
     * @param array $billingAddress Data for Billing Address
     *
     * @return void
     */
    protected function addBillingAddress(array $billingAddress)
    {
        /**
         * Order Address
         * @var \Extcode\Cart\Domain\Model\Order\Address $orderAddress
         */
        $orderAddress = $this->objectManager->get('Extcode\\Cart\\Domain\\Model\\Order\\Address');
        $orderAddress->setPid($this->storagePid);

        if ($billingAddress['title']) {
            $orderAddress->setTitle($billingAddress['title']);
        }
        $orderAddress->setSalutation($billingAddress['salutation']);
        $orderAddress->setFirstName($billingAddress['firstName']);
        $orderAddress->setLastName($billingAddress['lastName']);

        $this->addressRepository->add($orderAddress);

        $this->orderItem->setBillingAddress($orderAddress);
    }

    /**
     * Add Shipping Address
     *
     * @param array $shippingAddress Data for Shipping Address
     *
     * @return void
     */
    protected function addShippingAddress(array $shippingAddress)
    {
        /**
         * Order Address
         * @var \Extcode\Cart\Domain\Model\Order\Address $orderAddress
         */
        $orderAddress = $this->objectManager->get('Extcode\\Cart\\Domain\\Model\\Order\\Address');
        $orderAddress->setPid($this->storagePid);

        if ($shippingAddress['title']) {
            $orderAddress->setTitle($shippingAddress['title']);
        }
        $orderAddress->setSalutation($shippingAddress['salutation']);
        $orderAddress->setFirstName($shippingAddress['firstName']);
        $orderAddress->setLastName($shippingAddress['lastName']);

        $this->addressRepository->add($orderAddress);

        $this->orderItem->setBillingAddress($orderAddress);
    }

    /**
     * Add Payment
     *
     * @return void
     */
    protected function addPayment()
    {
        $payment = $this->cart->getPayment();

        /**
         * Order Payment
         * @var $orderPayment \Extcode\Cart\Domain\Model\Order\Payment
         */
        $orderPayment = $this->objectManager->get('Extcode\\Cart\\Domain\\Model\\Order\\Payment');
        $orderPayment->setPid($this->storagePid);

        $orderPayment->setName($payment->getName());
        $orderPayment->setProvider($payment->getProvider());
        $orderPayment->setStatus($payment->getStatus());
        $orderPayment->setGross($payment->getGross());
        $orderPayment->setNet($payment->getNet());
        $orderPayment->setTaxClass($this->taxClasses[$payment->getTaxClass()->getId()]);
        $orderPayment->setTax($payment->getTax());

        $this->paymentRepository->add($orderPayment);

        $this->orderItem->setPayment($orderPayment);
    }

    /**
     * Add Shipping
     *
     * @return void
     */
    protected function addShipping()
    {
        $shipping = $this->cart->getShipping();

        /**
         * Order Shipping
         * @var $orderShipping \Extcode\Cart\Domain\Model\Order\Shipping
         */
        $orderShipping = $this->objectManager->get('Extcode\\Cart\\Domain\\Model\\Order\\Shipping');
        $orderShipping->setPid($this->storagePid);

        $orderShipping->setName($shipping->getName());
        $orderShipping->setStatus($shipping->getStatus());
        $orderShipping->setGross($shipping->getGross());
        $orderShipping->setNet($shipping->getNet());
        $orderShipping->setTaxClass($this->taxClasses[$shipping->getTaxClass()->getId()]);
        $orderShipping->setTax($shipping->getTax());

        $this->shippingRepository->add($orderShipping);

        $this->orderItem->setShipping($orderShipping);
    }

    /**
     * Get Order Number
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return int
     */
    protected function getOrderNumber(array $pluginSettings)
    {
        /**
         * @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService
         */
        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginTypoScriptSettings = $typoScriptService->convertPlainArrayToTypoScriptArray($pluginSettings);

        $registry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Registry');

        $registryName = 'lastOrder_' . $pluginSettings['settings']['cart']['pid'];

        $orderNumber = $registry->get('tx_cart', $registryName);
        $orderNumber = $orderNumber ? $orderNumber + 1 : 1;
        $registry->set('tx_cart', $registryName, $orderNumber);

        $cObjRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
        $cObjRenderer->start(array('orderNumber' => $orderNumber));
        $orderNumber = $cObjRenderer->cObjGetSingle(
            $pluginTypoScriptSettings['orderNumber'],
            $pluginTypoScriptSettings['orderNumber.']
        );

        return $orderNumber;
    }

    /**
     * Get Invoice Number
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return int
     */
    public function getInvoiceNumber(array $pluginSettings)
    {
        /**
         * @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService
         */
        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginTypoScriptSettings = $typoScriptService->convertPlainArrayToTypoScriptArray($pluginSettings);


        $registry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Registry');

        $registryName = 'lastInvoice_' . $pluginSettings['settings']['cart']['pid'];

        $invoiceNumber = $registry->get('tx_cart', $registryName);
        $invoiceNumber = $invoiceNumber ? $invoiceNumber + 1 : 1;
        $registry->set('tx_cart', $registryName, $invoiceNumber);

        $cObjRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
        $cObjRenderer->start(array('invoiceNumber' => $invoiceNumber));
        $invoiceNumber = $cObjRenderer->cObjGetSingle(
            $pluginTypoScriptSettings['invoiceNumber'],
            $pluginTypoScriptSettings['invoiceNumber.']
        );

        return $invoiceNumber;
    }
}
