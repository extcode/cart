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
 * Parser Utility
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ParserUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

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
            $taxClasses[$taxClassKey] = $this->objectManager->get(\Extcode\Cart\Domain\Model\Cart\TaxClass::class,
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

            $taxes[$taxClassId] = $this->objectManager->get(\Extcode\Cart\Domain\Model\Cart\TaxClass::class,
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

        $pluginSettingsType = $this->getTypePluginSettings($pluginSettings, $cart, $type);

        if ($pluginSettingsType['options']) {
            foreach ($pluginSettingsType['options'] as $key => $value) {
                $class = 'Extcode\\Cart\\Domain\\Model\\Cart\\' . $className;
                /**
                 * Service
                 * @var \Extcode\Cart\Domain\Model\Cart\AbstractService $service
                 */
                $service = $this->objectManager->get($class,
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
                    unset($value['extra']['_typoScriptNodeValue']);
                    foreach ($value['extra'] as $extraKey => $extraValue) {
                        $extra = $this->objectManager->get(\Extcode\Cart\Domain\Model\Cart\Extra::class,
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
                    $extra = $this->objectManager->get(\Extcode\Cart\Domain\Model\Cart\Extra::class,
                        0,
                        0,
                        0,
                        $cart->getTaxClass($value['taxClassId']),
                        $cart->getIsNetCart()
                    );
                    $service->addExtra($extra);
                } else {
                    $service->setExtraType('simple');
                    $extra = $this->objectManager->get(\Extcode\Cart\Domain\Model\Cart\Extra::class,
                        0,
                        0,
                        $value['extra'],
                        $cart->getTaxClass($value['taxClassId']),
                        $cart->getIsNetCart()
                    );
                    $service->addExtra($extra);
                }

                if ($value['free']) {
                    $service->setFreeFrom($value['free']['from']);
                    $service->setFreeUntil($value['free']['until']);
                }
                if ($value['available']) {
                    $service->setAvailableFrom($value['available']['from']);
                    $service->setAvailableUntil($value['available']['until']);
                    if ($value['available']['fallBackId']) {
                        $service->setFallBackId($value['available']['fallBackId']);
                    }
                }

                if ($pluginSettingsType['preset'] == $key) {
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
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param $type
     *
     * @return mixed
     */
    public function getTypePluginSettings(array $pluginSettings, \Extcode\Cart\Domain\Model\Cart\Cart $cart, $type)
    {
        $pluginSettingsType = $pluginSettings[$type];
        $selectedCountry = $pluginSettings['settings']['defaultCountry'];

        if ($cart->getCountry()) {
            if ($type == 'payments') {
                $selectedCountry = $cart->getBillingCountry();
            } else {
                $selectedCountry = $cart->getCountry();
            }
        }

        if ($selectedCountry) {
            if (is_array($pluginSettingsType[$selectedCountry]) &&
                is_array($pluginSettingsType[$selectedCountry]['options'])
            ) {
                $pluginSettingsType = $pluginSettingsType[$selectedCountry];
                return $pluginSettingsType;
            }
            return $pluginSettingsType;
        }
        return $pluginSettingsType;
    }
}
