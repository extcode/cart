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
 * Parser Utility
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ParserUtility
{

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Parse Tax Classes
     *
     * @param array $pluginSettings Plugin Settings
     *
     * @return array $taxes
     */
    public function parseTaxClasses(array $pluginSettings)
    {
        $taxClasses = [];

        if (isset($pluginSettings['taxClassRepository']) && is_array($pluginSettings['taxClassRepository'])) {
            $taxClasses = $this->parseTaxClassesFromRepository($pluginSettings['taxClassRepository']);
        } elseif (isset($pluginSettings['taxClasses']) && is_array($pluginSettings['taxClasses'])) {
            $taxClasses = $this->parseTaxClassesFromTypoScript($pluginSettings['taxClasses']);
        }

        return $taxClasses;
    }

    /**
     * Parse Tax Classes From TypoScript
     *
     * @param array $taxClassSettings TypoScript Tax Class Settings
     *
     * @return array $taxes
     */
    protected function parseTaxClassesFromTypoScript(array $taxClassSettings)
    {
        $taxClasses = [];

        foreach ($taxClassSettings as $taxClassKey => $taxClassValue) {
            $taxClasses[$taxClassKey] = new \Extcode\Cart\Domain\Model\Cart\TaxClass(
                $taxClassKey,
                $taxClassValue['value'],
                $taxClassValue['calc'],
                $taxClassValue['name']
            );
        }

        return $taxClasses;
    }

    /**
     * Parse Tax Classes From Repository
     *
     * @param array $taxClassRepositorySettings TypoScript Tax Class Settings
     *
     * @return array $taxes
     */
    protected function parseTaxClassesFromRepository(array $taxClassRepositorySettings)
    {
        $taxes = [];

        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $taxClassRepository = $objectManager->get($taxClassRepositorySettings['class']);
        $taxClassObjects = $taxClassRepository->findAll();

        foreach ($taxClassObjects as $taxClassObject) {
            $taxClassId = $taxClassObject->$taxClassRepositorySettings['fields']['getId']();
            $taxClassValue = $taxClassObject->$taxClassRepositorySettings['fields']['getValue']();
            $taxClassCalc = $taxClassObject->$taxClassRepositorySettings['fields']['getCalc']();
            $taxClassName = $taxClassObject->$taxClassRepositorySettings['fields']['getTitle']();

            $taxes[$taxClassId] = new \Extcode\Cart\Domain\Model\Cart\TaxClass(
                $taxClassId,
                $taxClassValue,
                $taxClassCalc,
                $taxClassName
            );
        }

        return $taxes;
    }

    /**
     * Parse Services
     *
     * @param string $className
     * @param array $pluginSettings Plugin Settings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return array
     */
    public function parseServices($className, array $pluginSettings, \Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        $services = [];
        $type = strtolower($className) . 's';

        if ($pluginSettings[$type]['options']) {
            foreach ($pluginSettings[$type]['options'] as $key => $value) {
                $class = '\\Extcode\\Cart\\Domain\\Model\\Cart\\' . $className;
                /**
                 * Service
                 * @var \Extcode\Cart\Domain\Model\Cart\Service $service
                 */
                $service = new $class(
                    $key,
                    $value['title'],
                    $cart->getTaxClass($value['taxClassId']),
                    $value['status'],
                    $value['note'],
                    $cart->getIsNetCart()
                );

                if ($className == 'Payment') {
                    if ($value['provider']) {
                        $service->setProvider($value['provider']);
                    }
                }

                if (is_array($value['extra'])) {
                    $service->setExtraType($value['extra']['_typoScriptNodeValue']);
                    foreach ($value['extra'] as $extraKey => $extraValue) {
                        $extra = new \Extcode\Cart\Domain\Model\Cart\Extra(
                            $extraKey,
                            $extraValue['value'],
                            $extraValue['extra'],
                            $cart->getTaxClass($value['taxClassId']),
                            $cart->getIsNetCart()
                        );
                        $service->addExtra($extra);
                    }
                } elseif (!floatval($value['extra'])) {
                    $service->setExtraType($value['extra']);
                    $extra = new \Extcode\Cart\Domain\Model\Cart\Extra(
                        0,
                        0,
                        0,
                        $cart->getTaxClass($value['taxClassId']),
                        $cart->getIsNetCart()
                    );
                    $service->addExtra($extra);
                } else {
                    $service->setExtraType('simple');
                    $extra = new \Extcode\Cart\Domain\Model\Cart\Extra(
                        0,
                        0,
                        $value['extra'],
                        $cart->getTaxClass($value['taxClassId']),
                        $cart->getIsNetCart()
                    );
                    $service->addExtra($extra);
                }

                $service->setFreeFrom($value['free']['from']);
                $service->setFreeUntil($value['free']['until']);
                $service->setAvailableFrom($value['available']['from']);
                $service->setAvailableUntil($value['available']['until']);

                if ($pluginSettings[$type]['preset'] == $key) {
                    $service->setIsPreset(true);
                }

                $additional = [];
                if ($value['additional.']) {
                    foreach ($value['additional'] as $additionalKey => $additionalValue) {
                        if ($additionalValue['value']) {
                            $additional[$additionalKey] = $additionalValue['value'];
                        }
                    }
                }

                $service->setAdditionalArray($additional);
                $service->setCart($cart);

                $services[$key] = $service;
            }
        }

        return $services;
    }

    /**
     * @param array $pluginSettings
     * @param Request $request Request
     *
     * @return array
     */
    public function getPreCartProductSet(array $pluginSettings, Request $request)
    {
        if (!$this->pluginSettings) {
            $this->pluginSettings = $pluginSettings;
        }

        $productValueSet = [];

        if ($request->hasArgument('productId')) {
            $productValueSet['productId'] = intval($request->getArgument('productId'));
        }
        if ($request->hasArgument('tableId')) {
            $productValueSet['tableId'] = intval($request->getArgument('tableId'));
        }
        if ($request->hasArgument('repositoryId')) {
            $productValueSet['repositoryId'] = intval($request->getArgument('repositoryId'));
        }
        if ($request->hasArgument('contentId')) {
            $productValueSet['contentId'] = intval($request->getArgument('contentId'));
        }
        if ($request->hasArgument('quantity')) {
            $quantity = intval($request->getArgument('quantity'));
            $productValueSet['quantity'] = $quantity ? $quantity : 1;
        }

        if ($request->hasArgument('feVariants')) {
            $requestFeVariants = $request->getArgument('feVariants');
            if (is_array($requestFeVariants)) {
                foreach ($requestFeVariants as $requestFeVariantKey => $requestFeVariantValue) {
                    $productValueSet['feVariants'][$requestFeVariantKey] = $requestFeVariantValue;
                }
            }
        }

        if ($request->hasArgument('beVariants')) {
            $requestVariants = $request->getArgument('beVariants');
            if (is_array($requestVariants)) {
                foreach ($requestVariants as $requestVariantKey => $requestVariantValue) {
                    $productValueSet['beVariants'][$requestVariantKey] = intval($requestVariantValue);
                }
            }
        }

        return $productValueSet;
    }
}
