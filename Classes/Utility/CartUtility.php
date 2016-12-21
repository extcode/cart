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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Configuration\Exception;
use \TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Cart Utility
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Session Handler
     *
     * @var \Extcode\Cart\Service\SessionHandler
     * @inject
     */
    protected $sessionHandler;

    /**
     * Parser Utility
     *
     * @var \Extcode\Cart\Utility\ParserUtility
     * @inject
     */
    protected $parserUtility;

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
     * Restore cart from session or creates a new one
     *
     * @param array $cartSettings
     * @param array $pluginSettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getCartFromSession(array $cartSettings, array $pluginSettings)
    {
        $cart = $this->sessionHandler->restoreFromSession($cartSettings['pid']);

        if (!$cart) {
            $cart = $this->getNewCart($cartSettings, $pluginSettings);
        }

        return $cart;
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
     * Restore cart from session or creates a new one
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param array $cartSettings
     *
     * @return void
     */
    public function writeCartToSession($cart, $cartSettings)
    {
        $this->sessionHandler->writeToSession($cart, $cartSettings['cart']['pid']);
    }

    /**
     * Creates a new cart
     *
     * @param array $cartSettings TypoScript Cart Settings
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getNewCart(array $cartSettings, array $pluginSettings)
    {
        $isNetCart = intval($cartSettings['isNetCart']) == 0 ? false : true;

        $taxClasses = $this->parserUtility->parseTaxClasses($pluginSettings);

        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart($taxClasses, $isNetCart);

        $shippings = $this->parserUtility->parseServices('Shipping', $pluginSettings, $cart);

        foreach ($shippings as $shipping) {
            /**
             * Shipping
             * @var \Extcode\Cart\Domain\Model\Shipping $shipping
             */
            if ($shipping->getIsPreset()) {
                if (!$shipping->isAvailable($cart->getGross())) {
                    $fallBackId = $shipping->getFallBackId();
                    $shipping = $this->getServiceById($shippings, $fallBackId);
                }
                $cart->setShipping($shipping);
                break;
            }
        }

        $payments = $this->parserUtility->parseServices('Payment', $pluginSettings, $cart);

        foreach ($payments as $payment) {
            /**
             * Payment
             * @var \Extcode\Cart\Domain\Model\Payment $payment
             */
            if ($payment->getIsPreset()) {
                if (!$payment->isAvailable($cart->getGross())) {
                    $fallBackId = $payment->getFallBackId();
                    $payment = $this->getServiceById($payments, $fallBackId);
                }
                $cart->setPayment($payment);
                break;
            }
        }

        return $cart;
    }

    /**
     * @param array $services
     * @param int $serviceId
     *
     * @return mixed
     */
    public function getServiceById($services, $serviceId)
    {
        foreach ($services as $service) {
            if ($service->getId() == $serviceId) {
                return $service;
            }
        }

        return false;
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
        $cObjRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginTypoScriptSettings = $typoScriptService->convertPlainArrayToTypoScriptArray($pluginSettings);

        $registry = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Registry');

        $registryName = 'lastInvoice_' . $pluginSettings['settings']['cart']['pid'];
        $orderNumber = $registry->get('tx_cart', $registryName);

        $orderNumber = $orderNumber ? $orderNumber + 1 : 1;

        $registry->set('tx_cart', $registryName, $orderNumber);

        $cObjRenderer->start(['orderNumber' => $orderNumber]);
        $orderNumber = $cObjRenderer->
        cObjGetSingle($pluginTypoScriptSettings['orderNumber'], $pluginTypoScriptSettings['orderNumber.']);

        return $orderNumber;
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
            $preCartProductSets[1] = $this->parserUtility->getPreCartProductSet($pluginSettings, $request);
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
     * Create a CartProduct from array
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function createProduct(array $cartProductValues)
    {
        $newFeVariant = null;
        if ($cartProductValues['feVariants']) {
            $newFeVariant = new \Extcode\Cart\Domain\Model\Cart\FeVariant(
                $cartProductValues['feVariants']
            );
        }

        $newCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
            $cartProductValues['productType'],
            $cartProductValues['productId'],
            $cartProductValues['tableId'],
            $cartProductValues['contentId'],
            $cartProductValues['sku'],
            $cartProductValues['title'],
            $cartProductValues['price'],
            $this->taxClasses[$cartProductValues['taxClassId']],
            $cartProductValues['quantity'],
            $cartProductValues['isNetPrice'],
            $newFeVariant
        );

        if ($cartProductValues['maxNumber'] !== null) {
            $newCartProduct->setMaxNumberInCart($cartProductValues['maxNumber']);
        }
        if ($cartProductValues['minNumber'] !== null) {
            $newCartProduct->setMinNumberInCart($cartProductValues['minNumber']);
        }
        if ($cartProductValues['specialPrice'] !== null) {
            $newCartProduct->setSpecialPrice($cartProductValues['specialPrice']);
        }

        if ($cartProductValues['serviceAttribute1'] !== null) {
            $newCartProduct->setServiceAttribute1($cartProductValues['serviceAttribute1']);
        }
        if ($cartProductValues['serviceAttribute2'] !== null) {
            $newCartProduct->setServiceAttribute2($cartProductValues['serviceAttribute2']);
        }
        if ($cartProductValues['serviceAttribute3'] !== null) {
            $newCartProduct->setServiceAttribute3($cartProductValues['serviceAttribute3']);
        }

        $data = [
            'cartProductValues' => $cartProductValues,
            'newCartProduct' => $newCartProduct,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            'changeNewCartProduct',
            [$data]
        );

        $newCartProduct = $slotReturn[0]['newCartProduct'];

        $newVariantArr = [];

        // ToDo: refactor Variant

        if ($cartProductValues['beVariants']) {
            $variantConf = [];
            if (isset($this->pluginSettings['repository']) && is_array($this->pluginSettings['repository'])) {
                $variantConf = $this->pluginSettings;
            } elseif (isset($this->pluginSettings['db']) && is_array($this->pluginSettings['db'])) {
                $variantConf = $this->pluginSettings;
            }

            $priceCalcMethod = $cartProductValues['priceCalcMethod'];
            $price = $newCartProduct->getBestPrice();
            if ($this->pluginSettings['gpValues']['beVariants']) {
                foreach ($this->pluginSettings['gpValues']['beVariants'] as $variantsKey => $variantsValue) {
                    if ($variantsKey == 1) {
                        if ($cartProductValues['hasFeVariants']) {
                            $newVariant = $this->getFeVariant(
                                $newCartProduct,
                                null,
                                $cartProductValues,
                                $variantsValue,
                                $priceCalcMethod,
                                $price,
                                $cartProductValues['hasFeVariants'] - 1
                            );
                        } else {
                            if (isset($this->pluginSettings['repository'])) {
                                $variantConf = $variantConf['repository']['beVariants'];
                            } elseif (isset($this->pluginSettings['db'])) {
                                $variantConf = $variantConf['db']['beVariants'];
                            }

                            $newVariant = $this->getDatabaseVariant(
                                $newCartProduct,
                                null,
                                $variantConf,
                                $cartProductValues,
                                $variantsValue,
                                $priceCalcMethod,
                                $price
                            );
                        }

                        if ($newVariant) {
                            $newVariantArr[$variantsKey] = $newVariant;
                            $newCartProduct->addBeVariant($newVariant);
                            $price = $newVariant->getPrice();
                        } else {
                            break;
                        }
                    } elseif ($variantsKey > 1) {
                        // check if variant key-1 has fe_variants defined then use input as fe variant
                        if ($newVariantArr[$variantsKey - 1]->getHasFeVariants()) {
                            $newVariant = $this->getFeVariant(
                                null,
                                $newVariantArr[$variantsKey - 1],
                                $cartProductValues,
                                $variantsValue,
                                $priceCalcMethod,
                                $price,
                                $newVariantArr[$variantsKey - 1]->getHasFeVariants() - 1
                            );
                        } else {
                            if (isset($variantConf['repository'])) {
                                $variantConf = $variantConf['repository']['beVariants'];
                            } elseif (isset($variantConf['db'])) {
                                $variantConf = $variantConf['db']['beVariants'];
                            }

                            $newVariant = $this->getDatabaseVariant(
                                null,
                                $newVariantArr[$variantsKey - 1],
                                $variantConf,
                                $cartProductValues,
                                $variantsValue,
                                $priceCalcMethod,
                                $price
                            );
                        }

                        if ($newVariant) {
                            $newVariantArr[$variantsKey] = $newVariant;
                            $newVariantArr[$variantsKey - 1]->addBeVariant($newVariant);
                            $price = $newVariant->getPrice();
                        } else {
                            break;
                        }
                    }
                }
            }
        }
        return $newCartProduct;
    }

    /**
     * Get Variant Data From Database
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param $variantConf
     * @param $cartProductValues
     * @param $variantsValue
     * @param $priceCalcMethod
     * @param $price
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected function getDatabaseVariant(
        $product,
        $variant,
        $variantConf,
        $cartProductValues,
        $variantsValue,
        $priceCalcMethod,
        $price
    ) {
        list($pluginSettingsVariantsName, $pluginSettingsVariantsKey, $remainder) = explode('|', $variantsValue, 3);
        $variantsValue = $cartProductValues[$pluginSettingsVariantsName][$pluginSettingsVariantsKey];
        // if value is a integer, get details from database
        if (!is_int($variantsValue) ? (ctype_digit($variantsValue)) : true) {
            $variantId = $variantsValue;
            // creating a new Variant and using Price and Taxclass form CartProduct

            // get further data of variant
            $variantData = $this->getVariantDetails($cartProductValues, $variantId, $variantConf);

            if ($variantData['priceCalcMethod']) {
                $priceCalcMethod = intval($variantData['priceCalcMethod']);
            }

            $newCartVariant = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
                $variantsValue,
                $product,
                $variant,
                $variantData['title'],
                $variantData['sku'],
                $priceCalcMethod,
                $price,
                $cartProductValues['quantity']
            );

            if ($variantData['specialPrice']) {
                $newCartVariant->setSpecialPrice($variantData['specialPrice']);
            }

            unset($variantData['title']);
            unset($variantData['sku']);

            foreach ($variantData as $variantDataKey => $variantDataValue) {
                if (!is_array($variantDataValue)) {
                    $setter = 'set' . ucfirst($variantDataKey);
                    $newCartVariant->$setter($variantDataValue);
                }
            }
            $data = [
                'cartProductValues' => $cartProductValues,
                'newCartVariant' => $newCartVariant,
            ];

            $signalSlotDispatcher = $this->objectManager->get(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );
            $slotReturn = $signalSlotDispatcher->dispatch(
                __CLASS__,
                'changeNewCartVariant',
                [$data]
            );

            $newCartVariant = $slotReturn[0]['newCartVariant'];

            return $newCartVariant;
        }
    }

    /**
     * Get Frontend Variant
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param array $cartProductValues
     * @param string $variantsValue
     * @param integer $priceCalcMethod
     * @param float $price
     * @param integer $hasCountFeVariants
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected function getFeVariant(
        $product,
        $variant,
        array $cartProductValues,
        $variantsValue,
        $priceCalcMethod,
        $price,
        $hasCountFeVariants
    ) {
        $newVariant = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            sha1($variantsValue),
            $product,
            $variant,
            $variantsValue,
            str_replace(' ', '', $variantsValue),
            $priceCalcMethod,
            $price,
            $cartProductValues['quantity'],
            $cartProductValues['isNetPrice']
        );

        $newVariant->setHasFeVariants($hasCountFeVariants);

        return $newVariant;
    }

    /**
     * Get CartProduct from Content Element
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductFromCE(array $cartProductValues)
    {
        $abstractPlugin = new \TYPO3\CMS\Frontend\Plugin\AbstractPlugin();

        $row = $abstractPlugin->pi_getRecord('tt_content', $cartProductValues['contentId']);

        $flexformData = GeneralUtility::xml2array($row['pi_flexform']);

        $gpvarArr = ['productType', 'productId', 'sku', 'title', 'price', 'isNetPrice'];
        foreach ($gpvarArr as $gpvarVal) {
            $cartProductValues[$gpvarVal] = $abstractPlugin->pi_getFFvalue(
                $flexformData,
                'settings.' . $gpvarVal,
                'sDEF'
            );
        }

        $cartProductValues['taxClassId'] = $abstractPlugin->pi_getFFvalue(
            $flexformData,
            'settings.taxClassId',
            'sDEF'
        );

        $attributes = explode("\n", $abstractPlugin->pi_getFFvalue($flexformData, 'settings.attributes', 'sDEF'));

        foreach ($attributes as $line) {
            list($key, $value) = explode('==', $line, 2);
            switch ($key) {
                case 'serviceAttribute1':
                    $cartProductValues['serviceAttribute1'] = floatval($value);
                    break;
                case 'serviceAttribute2':
                    $cartProductValues['serviceAttribute2'] = floatval($value);
                    break;
                case 'serviceAttribute3':
                    $cartProductValues['serviceAttribute3'] = floatval($value);
                    break;
                case 'minNumber':
                    $cartProductValues['minNumber'] = intval($value);
                    break;
                case 'maxNumber':
                    $cartProductValues['maxNumber'] = intval($value);
                    break;
                default:
            }
        }

        return $this->createProduct($cartProductValues);
    }

    /**
     * Get CartProduct from Database
     *
     * @param array $cartProductValues
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductFromDatabase(array $cartProductValues)
    {
        if (isset($this->pluginSettings['repository']) && is_array($this->pluginSettings['repository'])) {
            return $this->getCartProductDetailsFromRepository(
                $cartProductValues,
                $this->pluginSettings['repository']
            );
        } elseif (isset($this->pluginSettings['db']) && is_array($this->pluginSettings['db'])) {
            return $this->getCartProductDetailsFromTable(
                $cartProductValues,
                $this->pluginSettings['db']
            );
        }

        return false;
    }

    /**
     * Get CartProduct from Database Table
     *
     * @param array $cartProductValues
     * @param array $databaseSettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductDetailsFromTable($cartProductValues, $databaseSettings)
    {
        $productId = intval($cartProductValues['productId']);

        $tableId = intval($cartProductValues['tableId']);
        if (($tableId != 0) && ($databaseSettings[$tableId])) {
            $databaseSettings = $databaseSettings[$tableId];
        }

        $table = $databaseSettings['table'];

        $l10nParent = $databaseSettings['l10n_parent'];

        $select = $this->getCartProductDataSelect($table, $databaseSettings['fields']);

        $where = ' ( ' . $table . '.uid = ' . $productId . ' OR ' . $table . '.' . $l10nParent . ' = ' . $productId . ' )' .
            ' AND sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_uid;
        //$where .= $this->contentObject->enableFields($table);
        $groupBy = '';
        $orderBy = '';
        $limit = 1;

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table, $where, $groupBy, $orderBy, $limit);

        if ($res) {
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            if ($databaseSettings['productType']) {
                if ($row[$databaseSettings['productType']]) {
                    $cartProductValues['productType'] = $row[$databaseSettings['productType']];
                }
            } else {
                $cartProductValues['productType'] = 'simple';
            }
            $cartProductValues['title'] = $row[$databaseSettings['title']];
            $cartProductValues['price'] = $row[$databaseSettings['price']];
            $cartProductValues['taxClassId'] = $row[$databaseSettings['taxClassId']];

            if ($row[$databaseSettings['sku']]) {
                $cartProductValues['sku'] = $row[$databaseSettings['sku']];
            }
            if ($row[$databaseSettings['serviceAttribute1']]) {
                $cartProductValues['serviceAttribute1'] = $row[$databaseSettings['serviceAttribute1']];
            }
            if ($row[$databaseSettings['serviceAttribute2']]) {
                $cartProductValues['serviceAttribute2'] = $row[$databaseSettings['serviceAttribute2']];
            }
            if ($row[$databaseSettings['serviceAttribute3']]) {
                $cartProductValues['serviceAttribute3'] = $row[$databaseSettings['serviceAttribute3']];
            }
            if ($row[$databaseSettings['serviceAttribute3']]) {
                $cartProductValues['serviceAttribute3'] = $row[$databaseSettings['serviceAttribute3']];
            }
            if ($row[$databaseSettings['hasFeVariants']]) {
                $cartProductValues['hasFeVariants'] = $row[$databaseSettings['hasFeVariants']];
            }
            if ($row[$databaseSettings['minNumber']]) {
                $cartProductValues['minNumber'] = $row[$databaseSettings['minNumber']];
            }
            if ($row[$databaseSettings['maxNumber']]) {
                $cartProductValues['maxNumber'] = $row[$databaseSettings['maxNumber']];
            }
            if ($row[$databaseSettings['specialPrice']]) {
                $cartProductValues['specialPrice'] = $row[$databaseSettings['specialPrice']];
            }

            if ($databaseSettings['additional']) {
                $cartProductValues['additional'] = [];
                foreach ($databaseSettings['additional'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $cartProductValues['additional'][$additionalKey] = $row[$additionalValue['field']];
                    } elseif ($additionalValue['value']) {
                        $cartProductValues['additional'][$additionalKey] = $additionalValue['value'];
                    }
                }
            }
        }

        return $this->createProduct($cartProductValues);
    }

    /**
     * Get CartProduct from Database Repository
     *
     * @param array $cartProductValues
     * @param array $repositorySettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductDetailsFromRepository($cartProductValues, $repositorySettings)
    {
        $productId = intval($cartProductValues['productId']);

        $repositoryId = intval($cartProductValues['repositoryId']);
        if (($repositoryId != 0) && ($repositorySettings[$repositoryId])) {
            $repositorySettings = $repositorySettings[$repositoryId];
        }

        $productRepository = $this->objectManager->get($repositorySettings['class']);
        $productObject = $productRepository->findByUid($productId);

        $repositoryFields = $repositorySettings['fields'];

        if ($productObject) {
            if (isset($repositoryFields['getProductType'])) {
                $functionName = $repositoryFields['getProductType'];
                $cartProductValues['productType'] = $productObject->$functionName();
            } else {
                $cartProductValues['productType'] = $productObject->getProductType();
            }
            if (isset($repositoryFields['getTitle'])) {
                $functionName = $repositoryFields['getTitle'];
                $cartProductValues['title'] = $productObject->$functionName();
            } else {
                $cartProductValues['title'] = $productObject->getTitle();
            }
            if (isset($repositoryFields['getSku'])) {
                $functionName = $repositoryFields['getSku'];
                $cartProductValues['sku'] = $productObject->$functionName();
            } else {
                $cartProductValues['sku'] = $productObject->getSku();
            }
            if (isset($repositoryFields['getPrice'])) {
                $functionName = $repositoryFields['getPrice'];
                $cartProductValues['price'] = $productObject->$functionName();
            } else {
                $cartProductValues['price'] = $productObject->getPrice();
            }

            if (isset($repositoryFields['getProductTaxClassId'])) {
                $functionName = $repositoryFields['getProductTaxClassId'];
                $cartProductValues['taxClassId'] = $productObject->$functionName();
            } elseif (isset($repositoryFields['getProductTaxClass'])) {
                $functionName = $repositoryFields['getProductTaxClass'];
                $cartProductValues['taxClassId'] = $productObject->$functionName()->getUid();
            } else {
                $cartProductValues['taxClassId'] = $productObject->getTaxClassId();
            }

            if (isset($repositoryFields['getServiceAttribute1'])) {
                $functionName = $repositoryFields['getServiceAttribute1'];
                $cartProductValues['serviceAttribute1'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getServiceAttribute2'])) {
                $functionName = $repositoryFields['getServiceAttribute2'];
                $cartProductValues['serviceAttribute2'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getServiceAttribute3'])) {
                $functionName = $repositoryFields['getServiceAttribute3'];
                $cartProductValues['serviceAttribute3'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['hasFeVariants'])) {
                $functionName = $repositoryFields['hasFeVariants'];
                $cartProductValues['hasFeVariants'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['getSpecialPrice'])) {
                $functionName = $repositoryFields['getSpecialPrice'];
                $frontendUserGroupIds = $this->getFrontendUserGroupIds();
                $cartProductValues['specialPrice'] = $productObject->$functionName($frontendUserGroupIds);
            }

            if (isset($repositoryFields['getMinNumber'])) {
                $functionName = $repositoryFields['getMinNumber'];
                $cartProductValues['minNumber'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getMaxNumber'])) {
                $functionName = $repositoryFields['getMaxNumber'];
                $cartProductValues['maxNumber'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['getFeVariants'])) {
                $feVariantValues = $cartProductValues['feVariants'];

                $functionName = $repositoryFields['getFeVariants'];
                $feVariants = $productObject->$functionName();

                if ($feVariants) {
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
            }

            if ($repositoryFields['additional.']) {
                $cartProductValues['additional'] = [];
                foreach ($repositoryFields['additional.'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $functionName = $additionalValue['field'];
                        $cartProductValues['additional'][$additionalKey] = $functionName();
                    } elseif ($additionalValue['value']) {
                        $cartProductValues['additional'][$additionalKey] = $additionalValue['value'];
                    }
                }
            }

        }

        $data = [
            'productObject' => $productObject,
            'repositoryFields' => $repositoryFields,
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

        return $this->createProduct($cartProductValues);
    }

    /**
     * @param string $table
     * @param $databaseFields
     *
     * @return string
     */
    protected function getCartProductDataSelect($table, $databaseFields)
    {
        $select = "";

        foreach ($databaseFields as $databaseFieldKey => $databaseFieldValue) {
            if ($databaseFieldValue != '' && is_string($databaseFieldValue)) {
                if ($databaseFieldValue != '{$plugin.tx_cart.db.' . $databaseFieldKey . '}') {
                    $select .= ', ' . $table . '.' . $databaseFieldValue;
                }
            }
        }

        if ($databaseFields['variants'] != '' && $databaseFields['variants'] != '{$plugin.tx_cart.db.variants}') {
            $select .= ', ' . $table . '.' . $databaseFields['variants'];
        }

        if ($databaseFields['additional.']) {
            foreach ($databaseFields['additional.'] as $additional) {
                if ($additional['field']) {
                    $select .= ', ' . $table . '.' . $additional['field'];
                }
            }
        }

        return $select;
    }

    /**
     * Get Variant Details
     *
     * @param array $cartProductValues
     * @param int $variantId
     * @param array $conf
     *
     * @return array
     *
     * @throws Exception
     */
    public function getVariantDetails($cartProductValues, $variantId, &$conf)
    {
        if (isset($conf['repository'])) {
            return $this->getVariantDetailsFromRepository($cartProductValues, $variantId, $conf['repository']);
        } elseif (isset($conf['db'])) {
            return $this->getVariantDetailsFromTable($variantId, $conf['db']);
        }

        throw new Exception;
    }

    /**
     * Get Variant Details From Table
     *
     * @param int $variantId
     * @param array $databaseSettings
     *
     * @return array $variantData
     */
    public function getVariantDetailsFromTable($variantId, $databaseSettings)
    {
        $table = $databaseSettings['table'];
        $l10nParent = $databaseSettings['l10n_parent'] ? $databaseSettings['l10n_parent'] : 'l10n_parent';

        $select = $table . '.' . $databaseSettings['title'];

        if ($databaseSettings['priceCalcMethod'] != '' &&
            $databaseSettings['priceCalcMethod'] != '{$plugin.cart.db.variants.db.priceCalcMethod}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['priceCalcMethod'];
        }
        if ($databaseSettings['price'] != '' &&
            $databaseSettings['price'] != '{$plugin.cart.db.variants.db.price}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['price'];
        }
        if ($databaseSettings['specialPrice'] != '' &&
            $databaseSettings['specialPrice'] != '{$plugin.cart.db.variants.db.specialPrice}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['specialPrice'];
        }
        if ($databaseSettings['inheritPrice'] != '' &&
            $databaseSettings['variants.']['db.']['price'] != '{$plugin.cart.db.variants.db.inheritPrice}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['inheritPrice'];
        }
        if ($databaseSettings['sku'] != '' &&
            $databaseSettings['sku'] != '{$plugin.cart.db.variants.db.sku}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['sku'];
        }
        if ($databaseSettings['hasFeVariants'] != '' &&
            $databaseSettings['hasFeVariants'] != '{$plugin.cart.db.variants.db.hasFeVariants}'
        ) {
            $select .= ', ' . $table . '.' . $databaseSettings['hasFeVariants'];
        }

        if ($databaseSettings['additional']) {
            foreach ($databaseSettings['additional'] as $additional) {
                if ($additional['field']) {
                    $select .= ', ' . $table . '.' . $additional['field'];
                }
            }
        }

        $where = ' ( ' . $table . '.uid = ' . $variantId . ' OR ' . $l10nParent . ' = ' . $variantId . ' )' .
            ' AND sys_language_uid = ' . $GLOBALS['TSFE']->sys_language_uid;
        $where .= tslib_cObj::enableFields($table);
        $groupBy = '';
        $orderBy = '';
        $limit = 1;

        if (TYPO3_DLOG) {
            $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = true;
        }

        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $table, $where, $groupBy, $orderBy, $limit);

        $variantData = [];

        if ($res) {
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);

            $variantData['title'] = $row[$databaseSettings['title']];
            if ($row[$databaseSettings['sku']]) {
                $variantData['sku'] = $row[$databaseSettings['sku']];
            }

            if ($row[$databaseSettings['priceCalcMethod']]) {
                $varianData['priceCalcMethod'] = $row[$databaseSettings['priceCalcMethod']];
            }

            // if inherit_price is defined then check the inherit_price and replace the with variant price
            // if inherit_price is not defined then replace the with variant price
            if ($databaseSettings['inheritPrice'] != '' &&
                $databaseSettings['price'] != '{$plugin.cart.db.variants.db.inheritPrice}'
            ) {
                if ($row[$databaseSettings['inheritPrice']]) {
                    if ($row[$databaseSettings['price']]) {
                        $variantData['price'] = $row[$databaseSettings['price']];
                    }
                }
            } else {
                if ($row[$databaseSettings['price']]) {
                    $variantData['price'] = $row[$databaseSettings['price']];
                }
            }

            if ($row[$databaseSettings['specialPrice']]) {
                $variantData['specialPrice'] = $row[$databaseSettings['specialPrice']];
            }

            if ($row[$databaseSettings['hasFeVariants']]) {
                $variantData['hasFeVariants'] = $row[$databaseSettings['hasFeVariants']];
            }

            if ($databaseSettings['additional']) {
                foreach ($databaseSettings['additional'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $variantData['additional'][$additionalKey] = $row[$additionalValue['field']];
                    } elseif ($additionalValue['value']) {
                        $variantData['additional'][$additionalKey] = $additionalValue['value'];
                    }
                }
            }
        }

        return $variantData;
    }

    /**
     * Get Variant Details From Repository
     *
     * @param array $cartProductValues
     * @param int $variantId
     * @param array $repositorySettings
     *
     * @return array $variantData
     */
    public function getVariantDetailsFromRepository($cartProductValues, $variantId, $repositorySettings)
    {
        $variantRepository = $this->objectManager->get($repositorySettings['class']);
        $variantObject = $variantRepository->findByUid($variantId);

        $cartVariantValues = [];
        if ($variantObject) {
            $repositoryFields = $repositorySettings['fields'];

            if (isset($repositoryFields['getTitle'])) {
                $functionName = $repositoryFields['getTitle'];
                $cartVariantValues['title'] = $variantObject->$functionName();
            } else {
                $cartVariantValues['title'] = $variantObject->getTitle();
            }

            if (isset($repositoryFields['getSku'])) {
                $functionName = $repositoryFields['getSku'];
                $cartVariantValues['sku'] = $variantObject->$functionName();
            } else {
                $cartVariantValues['sku'] = $variantObject->getSku();
            }

            if (isset($repositoryFields['getPriceCalcMethod'])) {
                $functionName = $repositoryFields['getPriceCalcMethod'];
                $cartVariantValues['priceCalcMethod'] = $variantObject->$functionName();
            } else {
                $cartVariantValues['priceCalcMethod'] = $variantObject->getPriceCalcMethod();
            }

            if (isset($repositoryFields['getPrice'])) {
                $functionName = $repositoryFields['getPrice'];
                $cartVariantValues['price'] = $variantObject->$functionName();
            } else {
                $cartVariantValues['price'] = $variantObject->getPrice();
            }

            if (isset($repositoryFields['getSpecialPrice'])) {
                $functionName = $repositoryFields['getSpecialPrice'];
                $frontendUserGroupIds = $this->getFrontendUserGroupIds();
                $cartVariantValues['specialPrice'] = $variantObject->$functionName($frontendUserGroupIds);
            }

            if (isset($repositoryFields['hasFeVariants'])) {
                $cartVariantValues['hasFeVariants'] = $repositoryFields['hasFeVariants'];
            }

            if (isset($repositoryFields['additional']) && is_array($repositoryFields['additional'])) {
                foreach ($repositoryFields['additional'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $cartVariantValues['additional']['$additionalKey'] = $variantObject->$additionalValue['field'];
                    } elseif ($additionalValue['value']) {
                        $cartVariantValues['additional']['$additionalKey'] = $additionalValue['value'];
                    }
                }
            }
        }

        $data = [
            'variantObject' => $variantObject,
            'repositoryFields' => $repositoryFields,
            'cartProductValues' => $cartProductValues,
            'cartVariantValues' => $cartVariantValues,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            'changeCartVariantValues',
            [$data]
        );

        $cartVariantValues = $slotReturn[0]['cartVariantValues'];

        return $cartVariantValues;
    }
}
