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
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Product Utility
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Tax Classes
     *
     * @var array
     */
    protected $taxClasses;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Get Frontend User Group
     *
     * @return array
     */
    protected function getFrontendUserGroupIds()
    {
        $feGroupIds = [];
        $feUserId = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        if ($feUserId) {
            $frontendUserRepository = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class
            );
            $feUser = $frontendUserRepository->findByUid($feUserId);
            $feGroups = $feUser->getUsergroup();
            if ($feGroups) {
                foreach ($feGroups as $feGroup) {
                    $feGroupIds[] = $feGroup->getUid();
                }
            }
        }
        return $feGroupIds;
    }

    /**
     * Get Cart/Product From Request
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     * @param Request $request Request
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass[] $taxClasses Tax Class Array
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product[]
     */
    public function getProductsFromRequest(array $pluginSettings, Request $request, array $taxClasses)
    {
        if (!$this->pluginSettings) {
            $this->pluginSettings = $pluginSettings;
        }
        if (!$this->taxClasses) {
            $this->taxClasses = $taxClasses;
        }

        $multiple = 1;
        if ($this->pluginSettings['multiple']) {
            $argumentName = $this->pluginSettings['multiple'];
            if ($request->hasArgument($argumentName)) {
                $multiple = intval($request->getArgument($argumentName));
            }
        }

        $products = [];
        $preCartProductSets = [];

        if ($multiple == 1) {
            $preCartProductSets[1] = $this->retrieveCartProductValuesFromRequest($pluginSettings, $request);
        } else {
            // TODO: iterate over request
        }

        foreach ($preCartProductSets as $preCartProductSetKey => $cartProductValues) {
            if ($cartProductValues['contentId']) {
                $products[$preCartProductSetKey] = $this->getCartProductFromCE($cartProductValues);
            } elseif ($cartProductValues['productId']) {
                $products[$preCartProductSetKey] = $this->getCartProductFromDatabase($cartProductValues);
            }
        }

        return $products;
    }

    /**
     * Get CartProduct from Content Element
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product|null
     */
    protected function getCartProductFromCE(array $cartProductValues)
    {
        $cartProduct = null;

        $abstractPlugin = $this->objectManager->get(\TYPO3\CMS\Frontend\Plugin\AbstractPlugin::class);

        $row = $abstractPlugin->pi_getRecord('tt_content', $cartProductValues['contentId']);

        $flexformData = GeneralUtility::xml2array($row['pi_flexform']);

        $gpvarArr = ['productType', 'productId', 'sku', 'title', 'price', 'taxClassId', 'isNetPrice'];
        foreach ($gpvarArr as $gpvarVal) {
            $cartProductValues[$gpvarVal] = $abstractPlugin->pi_getFFvalue(
                $flexformData,
                'settings.' . $gpvarVal,
                'sDEF'
            );
        }

        $cartProduct = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Cart\Product::class,
            $cartProductValues['productType'],
            $cartProductValues['productId'],
            null,
            $cartProductValues['contentId'],
            $cartProductValues['sku'],
            $cartProductValues['title'],
            $cartProductValues['price'],
            $this->taxClasses[$cartProductValues['taxClassId']],
            $cartProductValues['quantity'],
            $cartProductValues['isNetPrice'],
            null
        );

        $attributes = explode("\n", $abstractPlugin->pi_getFFvalue($flexformData, 'settings.attributes', 'sDEF'));

        foreach ($attributes as $line) {
            list($key, $value) = explode('==', $line, 2);
            switch ($key) {
                case 'serviceAttribute1':
                    $cartProduct->setServiceAttribute1(floatval($value));
                    break;
                case 'serviceAttribute2':
                    $cartProduct->setServiceAttribute2(floatval($value));
                    break;
                case 'serviceAttribute3':
                    $cartProduct->setServiceAttribute3(floatval($value));
                    break;
                case 'minNumber':
                    $cartProduct->setMinNumberInCart(intval($value));
                    break;
                case 'maxNumber':
                    $cartProduct->setMaxNumberInCart(intval($value));
                    break;
                default:
            }
        }

        return $cartProduct;
    }

    /**
     * Get CartProduct from Database
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product|null
     */
    protected function getCartProductFromDatabase(array $cartProductValues)
    {
        $cartProduct = null;
        $repositoryClass = '';

        $productStorageId = $cartProductValues['productStorageId'];

        if (is_array($this->pluginSettings['productStorages']) &&
            is_array($this->pluginSettings['productStorages'][$productStorageId]) &&
            isset($this->pluginSettings['productStorages'][$productStorageId]['class'])
        ) {
            $repositoryClass = $this->pluginSettings['productStorages'][$productStorageId]['class'];
        }

        if ($repositoryClass == 'Extcode\Cart\Domain\Repository\Product\ProductRepository') {
            $cartProduct = $this->createCartProduct($cartProductValues);
        } else {
            $cartProduct = $this->loadCartProductFromForeignDataStorage($cartProductValues, $productStorageId);
        }

        return $cartProduct;
    }

    /**
     * Get CartProduct from Database
     *
     * @param array $cartProductValues
     * @param int $productStorageId
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product|null
     */
    protected function loadCartProductFromForeignDataStorage(array $cartProductValues, $productStorageId)
    {
        $cartProduct = null;

        $data = [
            'cartProductValues' => $cartProductValues,
            'productStorageId' => $productStorageId,
            'cartProduct' => $cartProduct,
            'taxClasses' => $this->taxClasses,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );

        if ($slotReturn[0]['cartProduct'] instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
            $cartProduct = $slotReturn[0]['cartProduct'];
        }

        return $cartProduct;
    }

    /**
     * Create a CartProduct from array
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product|null
     */
    protected function createCartProduct(array $cartProductValues)
    {
        $cartProduct = null;

        $productId = intval($cartProductValues['productId']);

        $productRepository = $this->objectManager->get(
            \Extcode\Cart\Domain\Repository\Product\ProductRepository::class
        );

        /** @var \Extcode\Cart\Domain\Model\Product\Product $productProduct */
        $productProduct = $productRepository->findByUid($productId);

        if ($productProduct) {
            $frontendUserGroupIds = $this->getFrontendUserGroupIds();

            $feVariantValues = $cartProductValues['feVariants'];

            $feVariants = $productProduct->getFeVariants();

            if (count($feVariants)) {
                $cartProductValues['feVariants'] = [];
                foreach ($feVariants as $feVariant) {
                    if ($feVariantValues[$feVariant->getSku()]) {
                        $cartProductValues['feVariants'][] = [
                            'sku' => $feVariant->getSku(),
                            'title' => $feVariant->getTitle(),
                            'value' => $feVariantValues[$feVariant->getSku()]
                        ];
                    }
                }
            }

            $newFeVariant = null;
            if ($cartProductValues['feVariants']) {
                $newFeVariant = $this->objectManager->get(
                    \Extcode\Cart\Domain\Model\Cart\FeVariant::class,
                    $cartProductValues['feVariants']
                );
            }

            $cartProduct = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Cart\Product::class,
                $productProduct->getProductType(),
                $cartProductValues['productId'],
                $cartProductValues['productStorageId'],
                null,
                $productProduct->getSku(),
                $productProduct->getTitle(),
                $productProduct->getPrice(),
                $this->taxClasses[$productProduct->getTaxClassId()],
                $cartProductValues['quantity'],
                $productProduct->getIsNetPrice(),
                $newFeVariant
            );

            $cartProduct->setMaxNumberInCart($productProduct->getMaxNumberInOrder());
            $cartProduct->setMinNumberInCart($productProduct->getMinNumberInOrder());

            $cartProduct->setSpecialPrice($productProduct->getBestSpecialPrice($frontendUserGroupIds));
            $cartProduct->setQuantityDiscounts($productProduct->getQuantityDiscountArray($frontendUserGroupIds));

            $cartProduct->setServiceAttribute1($productProduct->getServiceAttribute1());
            $cartProduct->setServiceAttribute2($productProduct->getServiceAttribute2());
            $cartProduct->setServiceAttribute3($productProduct->getServiceAttribute3());

            if ($productProduct->getProductType() == 'virtual' || $productProduct->getProductType() == 'downloadable') {
                $cartProduct->setIsVirtualProduct(true);
            }

            $cartProduct->setAdditionalArray($cartProductValues['additional']);

            $data = [
                'cartProductValues' => $cartProductValues,
                'productProduct' => $productProduct,
                'cartProduct' => $cartProduct,
            ];

            $signalSlotDispatcher = $this->objectManager->get(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );
            $slotReturn = $signalSlotDispatcher->dispatch(
                __CLASS__,
                'changeNewCartProduct',
                [$data]
            );

            $cartProduct = $slotReturn[0]['cartProduct'];

            $newVariantArr = [];

            if ($cartProductValues['beVariants']) {
                if ($this->pluginSettings['gpValues']['beVariants']) {
                    foreach ($this->pluginSettings['gpValues']['beVariants'] as $variantsKey => $variantsValue) {
                        if ($variantsKey == 1) {
                            $newVariant = $this->createCartBackendVariant(
                                $cartProduct,
                                null,
                                $cartProductValues,
                                $variantsValue
                            );

                            if ($newVariant) {
                                $newVariantArr[$variantsKey] = $newVariant;
                                $cartProduct->addBeVariant($newVariant);
                            } else {
                                break;
                            }
                        } else {
                            $newVariant = $this->createCartBackendVariant(
                                null,
                                $newVariantArr[$variantsKey - 1],
                                $cartProductValues,
                                $variantsValue
                            );

                            if ($newVariant) {
                                $newVariantArr[$variantsKey] = $newVariant;
                                $newVariantArr[$variantsKey - 1]->addBeVariant($newVariant);
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return $cartProduct;
    }

    /**
     * Get Variant From Repository
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param $cartProductValues
     * @param $variantsValue
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant|null
     */
    protected function createCartBackendVariant(
        $product,
        $variant,
        $cartProductValues,
        $variantsValue
    ) {
        $cartBackendVariant = null;

        list($pluginSettingsVariantsName, $pluginSettingsVariantsKey, $remainder) = explode('|', $variantsValue, 3);

        $variantsValue = $cartProductValues[$pluginSettingsVariantsName][$pluginSettingsVariantsKey];
        // if value is a integer, get details from database
        if (!is_int($variantsValue) ? (ctype_digit($variantsValue)) : true) {
            $variantId = $variantsValue;
            // creating a new Variant and using Price and Taxclass form CartProduct

            // get further data of variant
            $variantRepository = $this->objectManager->get(
                \Extcode\Cart\Domain\Repository\Product\BeVariantRepository::class
            );
            /** @var \Extcode\Cart\Domain\Model\Product\BeVariant $productBackendVariant */
            $productBackendVariant = $variantRepository->findByUid($variantId);

            if ($productBackendVariant) {
                $frontendUserGroupIds = $this->getFrontendUserGroupIds();

                $bestSpecialPrice = $productBackendVariant->getBestSpecialPrice($frontendUserGroupIds);

                $cartBackendVariant = $this->objectManager->get(
                    \Extcode\Cart\Domain\Model\Cart\BeVariant::class,
                    $variantId,
                    $product,
                    $variant,
                    $productBackendVariant->getTitle(),
                    $productBackendVariant->getSku(),
                    $productBackendVariant->getPriceCalcMethod(),
                    $productBackendVariant->getPrice(),
                    $cartProductValues['quantity']
                );

                if ($bestSpecialPrice) {
                    $cartBackendVariant->setSpecialPrice($bestSpecialPrice->getPrice());
                }

                $data = [
                    'productBackendVariant' => $productBackendVariant,
                    'cartBackendVariant' => $cartBackendVariant,
                ];

                $signalSlotDispatcher = $this->objectManager->get(
                    \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
                );
                $slotReturn = $signalSlotDispatcher->dispatch(
                    __CLASS__,
                    'changeNewCartBeVariant',
                    [$data]
                );

                $cartBackendVariant = $slotReturn[0]['cartBackendVariant'];
            }
        }

        return $cartBackendVariant;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param array $products
     *
     * @return array
     */
    public function checkProductsBeforeAddToCart(\Extcode\Cart\Domain\Model\Cart\Cart $cart, $products)
    {
        $data = [
            'cart' => $cart,
            'products' => $products,
            'errors' => [],
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );

        $products = $slotReturn[0]['products'];
        $errors = $slotReturn[0]['errors'];

        return [$products, $errors];
    }

    /**
     * @param array $pluginSettings
     * @param Request $request Request
     *
     * @return array
     */
    public function retrieveCartProductValuesFromRequest(array $pluginSettings, Request $request)
    {
        if (!$this->pluginSettings) {
            $this->pluginSettings = $pluginSettings;
        }

        $cartProductValues = [];

        if ($request->hasArgument('productId')) {
            $cartProductValues['productId'] = intval($request->getArgument('productId'));
        }
        if ($request->hasArgument('productStorageId')) {
            $cartProductValues['productStorageId'] = intval($request->getArgument('productStorageId'));
        } else {
            $cartProductValues['productStorageId'] = 1;
        }

        if ($request->hasArgument('contentId')) {
            $cartProductValues['contentId'] = intval($request->getArgument('contentId'));
        }
        if ($request->hasArgument('quantity')) {
            $quantity = intval($request->getArgument('quantity'));
            $cartProductValues['quantity'] = $quantity ? $quantity : 1;
        }

        if ($request->hasArgument('feVariants')) {
            $requestFeVariants = $request->getArgument('feVariants');
            if (is_array($requestFeVariants)) {
                foreach ($requestFeVariants as $requestFeVariantKey => $requestFeVariantValue) {
                    $cartProductValues['feVariants'][$requestFeVariantKey] = $requestFeVariantValue;
                }
            }
        }

        if ($request->hasArgument('beVariants')) {
            $requestVariants = $request->getArgument('beVariants');
            if (is_array($requestVariants)) {
                foreach ($requestVariants as $requestVariantKey => $requestVariantValue) {
                    $cartProductValues['beVariants'][$requestVariantKey] = intval($requestVariantValue);
                }
            }
        }

        $data = [
            'pluginSettings' => $pluginSettings,
            'request' => $request,
            'cartProductValues' => $cartProductValues,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );

        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            'changeCartProductValues',
            [$data]
        );

        $cartProductValues = $slotReturn[0]['cartProductValues'];

        return $cartProductValues;
    }
}
