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
     * @inject
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
        if (!$this->objectManager) {
            $this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        }
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
                $cart->setPayment($payment);
                break;
            }
        }

        return $cart;
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

        foreach ($preCartProductSets as $preCartProductSetKey => $preCartProductSetValue) {
            if ($preCartProductSetValue['contentId']) {
                $products[$preCartProductSetKey] = $this->getCartProductFromCE($preCartProductSetValue);
            } elseif ($preCartProductSetValue['productId']) {
                $products[$preCartProductSetKey] = $this->getCartProductFromDatabase($preCartProductSetValue);
            }
        }

        return $products;
    }


    /**
     * Create a CartProduct from array
     *
     * @param array $preCartProductSetValue
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function createProduct(array $preCartProductSetValue)
    {
        $newFeVariant = null;
        if ($preCartProductSetValue['feVariants']) {
            $newFeVariant = new \Extcode\Cart\Domain\Model\Cart\FeVariant(
                $preCartProductSetValue['feVariants']
            );
        }

        $newCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
            $preCartProductSetValue['productType'],
            $preCartProductSetValue['productId'],
            $preCartProductSetValue['tableId'],
            $preCartProductSetValue['contentId'],
            $preCartProductSetValue['sku'],
            $preCartProductSetValue['title'],
            $preCartProductSetValue['price'],
            $this->taxClasses[$preCartProductSetValue['taxClassId']],
            $preCartProductSetValue['quantity'],
            $preCartProductSetValue['isNetPrice'],
            $newFeVariant
        );

        if ($preCartProductSetValue['maxNumber'] !== null) {
            $newCartProduct->setMaxNumberInCart($preCartProductSetValue['maxNumber']);
        }
        if ($preCartProductSetValue['minNumber'] !== null) {
            $newCartProduct->setMinNumberInCart($preCartProductSetValue['minNumber']);
        }
        if ($preCartProductSetValue['specialPrice'] !== null) {
            $newCartProduct->setSpecialPrice($preCartProductSetValue['specialPrice']);
        }

        if ($preCartProductSetValue['serviceAttribute1'] !== null) {
            $newCartProduct->setServiceAttribute1($preCartProductSetValue['serviceAttribute1']);
        }
        if ($preCartProductSetValue['serviceAttribute2'] !== null) {
            $newCartProduct->setServiceAttribute2($preCartProductSetValue['serviceAttribute2']);
        }
        if ($preCartProductSetValue['serviceAttribute3'] !== null) {
            $newCartProduct->setServiceAttribute3($preCartProductSetValue['serviceAttribute3']);
        }

        $newVariantArr = [];

        // ToDo: refactor Variant

        if ($preCartProductSetValue['beVariants']) {
            $variantConf = [];
            if (isset($this->pluginSettings['repository']) && is_array($this->pluginSettings['repository'])) {
                $variantConf = $this->pluginSettings;
            } elseif (isset($this->pluginSettings['db']) && is_array($this->pluginSettings['db'])) {
                $variantConf = $this->pluginSettings;
            }

            $priceCalcMethod = $preCartProductSetValue['priceCalcMethod'];
            $price = $newCartProduct->getBestPrice();
            if ($this->pluginSettings['gpValues']['beVariants']) {
                foreach ($this->pluginSettings['gpValues']['beVariants'] as $variantsKey => $variantsValue) {
                    if ($variantsKey == 1) {
                        if ($preCartProductSetValue['hasFeVariants']) {
                            $newVariant = $this->getFeVariant(
                                $newCartProduct,
                                null,
                                $preCartProductSetValue,
                                $variantsValue,
                                $priceCalcMethod,
                                $price,
                                $preCartProductSetValue['hasFeVariants'] - 1
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
                                $preCartProductSetValue,
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
                                $preCartProductSetValue,
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

                            $newVariant = $this->getDatabaseVariant(null, $newVariantArr[$variantsKey - 1],
                                $variantConf, $preCartProductSetValue, $variantsValue, $priceCalcMethod, $price);
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
     * @param $preCartProductSetValue
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
        $preCartProductSetValue,
        $variantsValue,
        $priceCalcMethod,
        $price
    ) {

        list($pluginSettingsVariantsName, $pluginSettingsVariantsKey, $remainder) = explode('|', $variantsValue, 3);
        $variantsValue = $preCartProductSetValue[$pluginSettingsVariantsName][$pluginSettingsVariantsKey];
        // if value is a integer, get details from database
        if (!is_int($variantsValue) ? (ctype_digit($variantsValue)) : true) {
            // creating a new Variant and using Price and Taxclass form CartProduct

            // get further data of variant
            $variantData = $this->getVariantDetails($variantsValue, $variantConf);

            if ($variantData['priceCalcMethod']) {
                $priceCalcMethod = intval($variantData['priceCalcMethod']);
            }

            $newVariant = new \Extcode\Cart\Domain\Model\Cart\BeVariant (
                $variantsValue,
                $product,
                $variant,
                $variantData['title'],
                $variantData['sku'],
                $priceCalcMethod,
                $price,
                $preCartProductSetValue['quantity']
            );

            unset($variantData['title']);
            unset($variantData['sku']);

            foreach ($variantData as $variantDataKey => $variantDataValue) {
                if (!is_array($variantDataValue)) {
                    $setter = 'set' . ucfirst($variantDataKey);
                    $newVariant->$setter($variantDataValue);
                }
            }

        }

        return $newVariant;
    }

    /**
     * Get Frontend Variant
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     * @param array $preCartProductSetValue
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
        array $preCartProductSetValue,
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
            $preCartProductSetValue['quantity'],
            $preCartProductSetValue['isNetPrice']
        );

        $newVariant->setHasFeVariants($hasCountFeVariants);

        return $newVariant;
    }

    /**
     * Get CartProduct from Content Element
     *
     * @param array $preCartProductSetValue
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductFromCE(array $preCartProductSetValue)
    {
        $abstractPlugin = new \TYPO3\CMS\Frontend\Plugin\AbstractPlugin();

        $row = $abstractPlugin->pi_getRecord('tt_content', $preCartProductSetValue['contentId']);

        $flexformData = GeneralUtility::xml2array($row['pi_flexform']);

        $gpvarArr = ['productType', 'productId', 'sku', 'title', 'price', 'isNetPrice'];
        foreach ($gpvarArr as $gpvarVal) {
            $preCartProductSetValue[$gpvarVal] = $abstractPlugin->pi_getFFvalue(
                $flexformData,
                'settings.' . $gpvarVal,
                'sDEF'
            );
        }

        $preCartProductSetValue['taxClassId'] = $abstractPlugin->pi_getFFvalue(
            $flexformData,
            'settings.taxClassId',
            'sDEF'
        );

        $attributes = explode("\n", $abstractPlugin->pi_getFFvalue($flexformData, 'settings.attributes', 'sDEF'));

        foreach ($attributes as $line) {
            list($key, $value) = explode('==', $line, 2);
            switch ($key) {
                case 'serviceAttribute1':
                    $preCartProductSetValue['serviceAttribute1'] = floatval($value);
                    break;
                case 'serviceAttribute2':
                    $preCartProductSetValue['serviceAttribute2'] = floatval($value);
                    break;
                case 'serviceAttribute3':
                    $preCartProductSetValue['serviceAttribute3'] = floatval($value);
                    break;
                case 'minNumber':
                    $preCartProductSetValue['minNumber'] = intval($value);
                    break;
                case 'maxNumber':
                    $preCartProductSetValue['maxNumber'] = intval($value);
                    break;
                default:
            }
        }

        return $this->createProduct($preCartProductSetValue);
    }

    /**
     * Get CartProduct from Database
     *
     * @param array $preCartProductSetValue
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductFromDatabase(array $preCartProductSetValue)
    {

        if (isset($this->pluginSettings['repository']) && is_array($this->pluginSettings['repository'])) {
            return $this->getCartProductDetailsFromRepository($preCartProductSetValue,
                $this->pluginSettings['repository']);
        } elseif (isset($this->pluginSettings['db']) && is_array($this->pluginSettings['db'])) {
            return $this->getCartProductDetailsFromTable($preCartProductSetValue, $this->pluginSettings['db']);
        }

        return false;
    }

    /**
     * Get CartProduct from Database Table
     *
     * @param array $preCartProductSetValue
     * @param array $databaseSettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductDetailsFromTable($preCartProductSetValue, $databaseSettings)
    {
        $productId = intval($preCartProductSetValue['productId']);

        $tableId = intval($preCartProductSetValue['tableId']);
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
                    $preCartProductSetValue['productType'] = $row[$databaseSettings['productType']];
                }
            } else {
                $preCartProductSetValue['productType'] = 'simple';
            }
            $preCartProductSetValue['title'] = $row[$databaseSettings['title']];
            $preCartProductSetValue['price'] = $row[$databaseSettings['price']];
            $preCartProductSetValue['taxClassId'] = $row[$databaseSettings['taxClassId']];

            if ($row[$databaseSettings['sku']]) {
                $preCartProductSetValue['sku'] = $row[$databaseSettings['sku']];
            }
            if ($row[$databaseSettings['serviceAttribute1']]) {
                $preCartProductSetValue['serviceAttribute1'] = $row[$databaseSettings['serviceAttribute1']];
            }
            if ($row[$databaseSettings['serviceAttribute2']]) {
                $preCartProductSetValue['serviceAttribute2'] = $row[$databaseSettings['serviceAttribute2']];
            }
            if ($row[$databaseSettings['serviceAttribute3']]) {
                $preCartProductSetValue['serviceAttribute3'] = $row[$databaseSettings['serviceAttribute3']];
            }
            if ($row[$databaseSettings['serviceAttribute3']]) {
                $preCartProductSetValue['serviceAttribute3'] = $row[$databaseSettings['serviceAttribute3']];
            }
            if ($row[$databaseSettings['hasFeVariants']]) {
                $preCartProductSetValue['hasFeVariants'] = $row[$databaseSettings['hasFeVariants']];
            }
            if ($row[$databaseSettings['minNumber']]) {
                $preCartProductSetValue['minNumber'] = $row[$databaseSettings['minNumber']];
            }
            if ($row[$databaseSettings['maxNumber']]) {
                $preCartProductSetValue['maxNumber'] = $row[$databaseSettings['maxNumber']];
            }
            if ($row[$databaseSettings['specialPrice']]) {
                $preCartProductSetValue['specialPrice'] = $row[$databaseSettings['specialPrice']];
            }

            if ($databaseSettings['additional']) {
                $preCartProductSetValue['additional'] = [];
                foreach ($databaseSettings['additional'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $preCartProductSetValue['additional'][$additionalKey] = $row[$additionalValue['field']];
                    } elseif ($additionalValue['value']) {
                        $preCartProductSetValue['additional'][$additionalKey] = $additionalValue['value'];
                    }
                }
            }
        }

        return $this->createProduct($preCartProductSetValue);
    }

    /**
     * Get CartProduct from Database Repository
     *
     * @param array $preCartProductSetValue
     * @param array $repositorySettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getCartProductDetailsFromRepository($preCartProductSetValue, $repositorySettings)
    {
        $productId = intval($preCartProductSetValue['productId']);

        $repositoryId = intval($preCartProductSetValue['repositoryId']);
        if (($repositoryId != 0) && ($repositorySettings[$repositoryId])) {
            $repositorySettings = $repositorySettings[$repositoryId];
        }

        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $productRepository = $objectManager->get($repositorySettings['class']);
        $productObject = $productRepository->findByUid($productId);

        $repositoryFields = $repositorySettings['fields'];

        if ($productObject) {
            if (isset($repositoryFields['getProductType'])) {
                $functionName = $repositoryFields['getProductType'];
                $preCartProductSetValue['productType'] = $productObject->$functionName();
            } else {
                $preCartProductSetValue['productType'] = $productObject->getProductType();
            }
            if (isset($repositoryFields['getTitle'])) {
                $functionName = $repositoryFields['getTitle'];
                $preCartProductSetValue['title'] = $productObject->$functionName();
            } else {
                $preCartProductSetValue['title'] = $productObject->getTitle();
            }
            if (isset($repositoryFields['getSku'])) {
                $functionName = $repositoryFields['getSku'];
                $preCartProductSetValue['sku'] = $productObject->$functionName();
            } else {
                $preCartProductSetValue['sku'] = $productObject->getSku();
            }
            if (isset($repositoryFields['getPrice'])) {
                $functionName = $repositoryFields['getPrice'];
                $preCartProductSetValue['price'] = $productObject->$functionName();
            } else {
                $preCartProductSetValue['price'] = $productObject->getPrice();
            }

            if (isset($repositoryFields['getProductTaxClassId'])) {
                $functionName = $repositoryFields['getProductTaxClassId'];
                $preCartProductSetValue['taxClassId'] = $productObject->$functionName();
            } elseif (isset($repositoryFields['getProductTaxClass'])) {
                $functionName = $repositoryFields['getProductTaxClass'];
                $preCartProductSetValue['taxClassId'] = $productObject->$functionName()->getUid();
            } else {
                $preCartProductSetValue['taxClassId'] = $productObject->getTaxClassId();
            }

            if (isset($repositoryFields['getServiceAttribute1'])) {
                $functionName = $repositoryFields['getServiceAttribute1'];
                $preCartProductSetValue['serviceAttribute1'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getServiceAttribute2'])) {
                $functionName = $repositoryFields['getServiceAttribute2'];
                $preCartProductSetValue['serviceAttribute2'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getServiceAttribute3'])) {
                $functionName = $repositoryFields['getServiceAttribute3'];
                $preCartProductSetValue['serviceAttribute3'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['hasFeVariants'])) {
                $functionName = $repositoryFields['hasFeVariants'];
                $preCartProductSetValue['hasFeVariants'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['getSpecialPrice'])) {
                $functionName = $repositoryFields['getSpecialPrice'];
                $frontendUserGroupIds = $this->getFrontendUserGroupIds();
                $preCartProductSetValue['specialPrice'] = $productObject->$functionName($frontendUserGroupIds);
            }

            if (isset($repositoryFields['getMinNumber'])) {
                $functionName = $repositoryFields['getMinNumber'];
                $preCartProductSetValue['minNumber'] = $productObject->$functionName();
            }
            if (isset($repositoryFields['getMaxNumber'])) {
                $functionName = $repositoryFields['getMaxNumber'];
                $preCartProductSetValue['maxNumber'] = $productObject->$functionName();
            }

            if (isset($repositoryFields['getFeVariants'])) {
                $feVariantValues = $preCartProductSetValue['feVariants'];

                $functionName = $repositoryFields['getFeVariants'];
                $feVariants = $productObject->$functionName();

                if ($feVariants) {
                    $preCartProductSetValue['feVariants'] = [];
                    foreach ($feVariants as $feVariant) {
                        if ($feVariantValues[$feVariant->getSku()]) {
                            $preCartProductSetValue['feVariants'][] = [
                                'sku' => $feVariant->getSku(),
                                'title' => $feVariant->getTitle(),
                                'value' => $feVariantValues[$feVariant->getSku()]
                            ];
                        }
                    }
                }
            }

            if ($repositoryFields['additional.']) {
                $preCartProductSetValue['additional'] = [];
                foreach ($repositoryFields['additional.'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $functionName = $additionalValue['field'];
                        $preCartProductSetValue['additional'][$additionalKey] = $functionName();
                    } elseif ($additionalValue['value']) {
                        $preCartProductSetValue['additional'][$additionalKey] = $additionalValue['value'];
                    }
                }
            }

        }

        return $this->createProduct($preCartProductSetValue);
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
     * @param int $variantId
     * @param array $conf
     * @return array
     * @throws Exception
     */
    public function getVariantDetails($variantId, &$conf)
    {

        if (isset($conf['repository'])) {
            return $this->getVariantDetailsFromRepository($variantId, $conf['repository']);
        } elseif (isset($conf['db'])) {
            return $this->getVariantDetailsFromTable($variantId, $conf['db']);
        }

        throw new Exception;
    }

    /**
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
            if ($databaseSettings['inheritPrice'] != '' && $databaseSettings['price'] != '{$plugin.cart.db.variants.db.inheritPrice}') {
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
     * @param int $variantId
     * @param array $repositorySettings
     *
     * @return array $variantData
     */
    public function getVariantDetailsFromRepository($variantId, $repositorySettings)
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $variantRepository = $objectManager->get($repositorySettings['class']);
        $variantObject = $variantRepository->findByUid($variantId);

        $variantData = [];
        if ($variantObject) {

            if (isset($variantObject->$repositorySettings['getTitle'])) {
                $variantData['title'] = $variantObject->$repositorySettings['getTitle'];
            } else {
                $variantData['title'] = $variantObject->getTitle();
            }

            if (isset($variantObject->$repositorySettings['getSku'])) {
                $variantData['sku'] = $variantObject->$repositorySettings['getSku'];
            } else {
                $variantData['sku'] = $variantObject->getSku();
            }

            if (isset($variantObject->$repositorySettings['getPriceCalcMethod'])) {
                $variantData['priceCalcMethod'] = $variantObject->$repositorySettings['getPriceCalcMethod'];
            } else {
                $variantData['priceCalcMethod'] = $variantObject->getPriceCalcMethod();
            }

            if (isset($variantObject->$repositorySettings['getPrice'])) {
                $variantData['price'] = $variantObject->$repositorySettings['getPrice'];
            } else {
                $variantData['price'] = $variantObject->getPrice();
            }

            if (isset($repositorySettings['hasFeVariants'])) {
                $variantData['hasFeVariants'] = $repositorySettings['hasFeVariants'];
            }

            if (isset($repositorySettings['additional']) && is_array($repositorySettings['additional'])) {
                foreach ($repositorySettings['additional'] as $additionalKey => $additionalValue) {
                    if ($additionalValue['field']) {
                        $variantData['additional']['$additionalKey'] = $variantObject->$additionalValue['field'];
                    } elseif ($additionalValue['value']) {
                        $variantData['additional']['$additionalKey'] = $additionalValue['value'];
                    }
                }
            }

            /*
                        // if inherit_price is defined then check the inherit_price and replace the with variant price
                        // if inherit_price is not defined then replace the with variant price
                        if ($conf['db.']['inherit_price'] != '' && $conf['db.']['price'] != '{$plugin.wtcart.db.variants.db.inherit_price}') {
                            if ($row[$conf['db.']['inherit_price']]) {
                                if ($row[$conf['db.']['price']]) {
                                    $variant->setPrice($row[$conf['db.']['price']]);
                                }
                            }
                        } else {
                            if ($row[$conf['db.']['price']]) {
                                $variant->setPrice($row[$conf['db.']['price']]);
                            }
                        }
            */
        }

        return $variantData;

    }
}
