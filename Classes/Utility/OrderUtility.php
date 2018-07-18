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
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class OrderUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $persistenceManager;

    /**
     * Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $orderItemRepository;

    /**
     * Product Coupon Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Product\CouponRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $productCouponRepository;

    /**
     * Order Discount Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\DiscountRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $orderDiscountRepository;

    /**
     * Product Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ProductRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $productRepository;

    /**
     * Product Additional Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ProductAdditionalRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $productAdditionalRepository;

    /**
     * Address Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\AddressRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $addressRepository;

    /**
     * Payment Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\PaymentRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $paymentRepository;

    /**
     * Shipping Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ShippingRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $shippingRepository;

    /**
     * Tax Class Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\TaxClassRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $taxClassRepository;

    /**
     * Order Tax Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\TaxRepository
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $taxRepository;

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
    protected $taxClasses;

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
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Save Order
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     */
    public function saveOrderItem(
        array $pluginSettings,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart,
        \Extcode\Cart\Domain\Model\Order\Item $orderItem
    ) {
        $this->storagePid = $pluginSettings['settings']['order']['pid'];

        $this->cart = $cart;
        $this->orderItem = $orderItem;

        $orderItem->setPid($this->storagePid);

        $orderItem->setFeUser((int)$GLOBALS['TSFE']->fe_user->user['uid']);

        $orderItem->setCurrency($pluginSettings['settings']['format']['currency']['currencySign']);
        $orderItem->setCurrencyCode($this->cart->getCurrencyCode());
        $orderItem->setCurrencySign($this->cart->getCurrencySign());
        $orderItem->setCurrencyTranslation($this->cart->getCurrencyTranslation());
        $orderItem->setGross($this->cart->getGross());
        $orderItem->setNet($this->cart->getNet());
        $orderItem->setTotalGross($this->cart->getTotalGross());
        $orderItem->setTotalNet($this->cart->getTotalNet());

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

        $data = [
            'cart' => $this->cart,
            'orderItem' => $this->orderItem,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            'changeOrderItemBeforeSaving',
            [$data]
        );

        $orderItem = $slotReturn[0]['orderItem'];

        $this->persistenceManager->persistAll();
    }

    /**
     * Adds a Taxes To Order
     *
     * @param string $type Type of the Tax
     */
    protected function addTaxes($type = 'Tax')
    {
        $cartTaxes = call_user_func([$this->cart, 'get' . $type . 'es']);
        foreach ($cartTaxes as $cartTaxKey => $cartTax) {
            /**
             * Order Tax
             * @var $orderTax \Extcode\Cart\Domain\Model\Order\Tax
             */
            $orderTax = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\Tax::class,
                $cartTax,
                $this->taxClasses[$cartTaxKey]
            );
            $orderTax->setPid($this->storagePid);

            $this->taxRepository->add($orderTax);

            call_user_func([$this->orderItem, 'add' . $type], $orderTax);
        }
    }

    /**
     * Add TaxClasses to Order Item
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
            $orderTaxClass = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\TaxClass::class,
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
                $orderDiscount = $this->objectManager->get(
                    \Extcode\Cart\Domain\Model\Order\Discount::class,
                    $cartCoupon->getTitle(),
                    $cartCoupon->getCode(),
                    $cartCoupon->getGross(),
                    $cartCoupon->getNet(),
                    $cartCoupon->getTaxClass(),
                    $cartCoupon->getTax()
                );
                $orderDiscount->setPid($this->storagePid);

                $this->orderDiscountRepository->add($orderDiscount);

                $this->orderItem->addDiscount($orderDiscount);

                $coupon = $this->productCouponRepository->findOneByCode($cartCoupon->getCode());
                $coupon->incNumberUsed();
                $this->productCouponRepository->update($coupon);
            }
        }
    }

    /**
     * Add Products to Order Item
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
     */
    protected function addProduct(\Extcode\Cart\Domain\Model\Cart\Product $cartProduct)
    {
        /**
         * @var \Extcode\Cart\Domain\Model\Order\Product $orderProduct
         */
        $orderProduct = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Product::class,
            $cartProduct->getSku(),
            $cartProduct->getTitle(),
            $cartProduct->getQuantity()
        );
        $orderProduct->setPid($this->storagePid);

        $orderProduct->setProductType($cartProduct->getProductType());
        $orderProduct->setPrice($cartProduct->getTranslatedPrice());
        $orderProduct->setDiscount($cartProduct->getDiscount());
        $orderProduct->setGross($cartProduct->getGross());
        $orderProduct->setNet($cartProduct->getNet());
        $orderProduct->setTaxClass($this->taxClasses[$cartProduct->getTaxClass()->getId()]);
        $orderProduct->setTax($cartProduct->getTax());

        $additionalArray = $cartProduct->getAdditionalArray();

        $data = [
            'cartProduct' => $cartProduct,
            'orderProduct' => &$orderProduct,
            'additionalArray' => &$additionalArray,
            'storagePid' => $this->storagePid,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__ . 'AdditionalData',
            [$data]
        );

        $orderProduct->setAdditional($data['additionalArray']);

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
        $productAdditional = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\ProductAdditional::class,
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
     */
    protected function addBeVariant(\Extcode\Cart\Domain\Model\Cart\BeVariant $variant, $level)
    {
        /** @var \Extcode\Cart\Domain\Model\Order\Tax $orderTax */
        $orderTax = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Tax::class,
            $variant->getTax(),
            $this->taxClasses[$variant->getTaxClass()->getId()]
        );
        $orderTax->setPid($this->storagePid);

        $this->taxRepository->add($orderTax);

        /**
         * Order Product
         * @var \Extcode\Cart\Domain\Model\Order\Product $orderProduct
         */
        $orderProduct = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Product::class,
            $variant->getCompleteSku(),
            $variant->getCompleteTitle(),
            $variant->getQuantity()
        );
        $orderProduct->setPid($this->storagePid);

        $skuWithVariants = [];
        $titleWithVariants = [];

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

        $orderProduct->setProductType($cartProduct->getProductType());
        $orderProduct->setPrice($variant->getPriceCalculated());
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
            $orderProductAdditional = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\ProductAdditional::class,
                'variant_' . $count,
                $variantInner->getCompleteSku(),
                $variantInner->getTitle()
            );
            $orderProductAdditional->setAdditional($variantInner->getAdditionalArray());
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

        $data = [
            'cartProduct' => $cartProduct,
            'orderProduct' => &$orderProduct,
            'additionalArray' => &$additionalArray,
            'storagePid' => $this->storagePid,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__ . 'AdditionalData',
            [$data]
        );

        $orderProduct->setAdditionalData(json_encode($data['additionalArray']));

        $this->productRepository->add($orderProduct);

        $this->orderItem->addProduct($orderProduct);
    }

    /**
     * Add Billing Address
     *
     * @param array $billingAddress Data for Billing Address
     */
    protected function addBillingAddress(array $billingAddress)
    {
        /**
         * Order Address
         * @var \Extcode\Cart\Domain\Model\Order\Address $orderAddress
         */
        $orderAddress = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Address::class
        );
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
     */
    protected function addShippingAddress(array $shippingAddress)
    {
        /**
         * Order Address
         * @var \Extcode\Cart\Domain\Model\Order\Address $orderAddress
         */
        $orderAddress = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Address::class
        );
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
     */
    protected function addPayment()
    {
        $payment = $this->cart->getPayment();

        /**
         * Order Payment
         * @var $orderPayment \Extcode\Cart\Domain\Model\Order\Payment
         */
        $orderPayment = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Payment::class
        );
        $orderPayment->setPid($this->storagePid);

        if ($this->cart->getBillingCountry()) {
            $orderPayment->setServiceCountry($this->cart->getBillingCountry());
        }
        $orderPayment->setServiceId($payment->getId());
        $orderPayment->setName($payment->getName());
        $orderPayment->setProvider($payment->getProvider());
        $orderPayment->setStatus($payment->getStatus());
        $orderPayment->setGross($payment->getGross());
        $orderPayment->setNet($payment->getNet());
        $orderPayment->setTaxClass($this->taxClasses[$payment->getTaxClass()->getId()]);
        $orderPayment->setTax($payment->getTax());
        $orderPayment->setNote($payment->getNote());

        $this->paymentRepository->add($orderPayment);

        $this->orderItem->setPayment($orderPayment);
    }

    /**
     * Add Shipping
     */
    protected function addShipping()
    {
        $shipping = $this->cart->getShipping();

        /**
         * Order Shipping
         * @var $orderShipping \Extcode\Cart\Domain\Model\Order\Shipping
         */
        $orderShipping = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Order\Shipping::class
        );
        $orderShipping->setPid($this->storagePid);

        if ($this->cart->getShippingCountry()) {
            $orderShipping->setServiceCountry($this->cart->getShippingCountry());
        }
        $orderShipping->setServiceId($shipping->getId());
        $orderShipping->setName($shipping->getName());
        $orderShipping->setStatus($shipping->getStatus());
        $orderShipping->setGross($shipping->getGross());
        $orderShipping->setNet($shipping->getNet());
        $orderShipping->setTaxClass($this->taxClasses[$shipping->getTaxClass()->getId()]);
        $orderShipping->setTax($shipping->getTax());
        $orderShipping->setNote($shipping->getNote());

        $this->shippingRepository->add($orderShipping);

        $this->orderItem->setShipping($orderShipping);
    }

    /**
     * Get Order Number
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return string
     */
    protected function getOrderNumber(array $pluginSettings)
    {
        return $this->getNumber($pluginSettings, 'order');
    }

    /**
     * Get Invoice Number
     *
     * @param array $pluginSettings
     * @param string $numberType
     *
     * @return string
     */
    public function getNumber(array $pluginSettings, $numberType)
    {
        /**
         * @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService
         */
        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginTypoScriptSettings = $typoScriptService->convertPlainArrayToTypoScriptArray($pluginSettings);

        $registry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Registry');

        $registryName = 'last' . ucfirst($numberType) . '_' . $pluginSettings['settings']['cart']['pid'];

        $number = $registry->get('tx_cart', $registryName);
        $number = $number ? $number + 1 : 1;
        $registry->set('tx_cart', $registryName, $number);

        $cObjRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');
        $cObjRenderer->start([$numberType . 'Number' => $number]);
        $number = $cObjRenderer->cObjGetSingle(
            $pluginTypoScriptSettings[$numberType . 'Number'],
            $pluginTypoScriptSettings[$numberType . 'Number.']
        );

        return $number;
    }
}
