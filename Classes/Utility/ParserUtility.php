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
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;

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
     * @param array $pluginSettings
     * @param string $countryCode
     *
     * @return array
     */
    public function parseTaxClasses(array $pluginSettings, $countryCode)
    {
        $taxClasses = [];

        if (isset($pluginSettings['taxClassRepository']) && is_array($pluginSettings['taxClassRepository'])) {
            $taxClasses = $this->loadTaxClassesFromForeignDataStorage($pluginSettings['taxClassRepository'], $countryCode);
        } elseif (isset($pluginSettings['taxClasses']) && is_array($pluginSettings['taxClasses'])) {
            $taxClasses = $this->parseTaxClassesFromTypoScript($pluginSettings['taxClasses'], $countryCode);
        }

        return $taxClasses;
    }

    /**
     * Parse Tax Classes From TypoScript
     *
     * @param array $taxClassSettings
     * @param string $countryCode
     *
     * @return array $taxes
     */
    protected function parseTaxClassesFromTypoScript(array $taxClassSettings, $countryCode)
    {
        $taxClasses = [];

        if ($countryCode && is_array($taxClassSettings[$countryCode])) {
            $taxClassSettings = $taxClassSettings[$countryCode];
        } elseif ($taxClassSettings['fallback'] && is_array($taxClassSettings['fallback'])) {
            $taxClassSettings = $taxClassSettings['fallback'];
        }

        foreach ($taxClassSettings as $taxClassKey => $taxClassValue) {
            $taxClasses[$taxClassKey] = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Cart\TaxClass::class,
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
     * @param array $taxClassRepositorySettings
     * @param string $countryCode
     *
     * @return array
     */
    protected function loadTaxClassesFromForeignDataStorage(array $taxClassRepositorySettings, $countryCode)
    {
        $taxes = [];

        $data = [
            'taxClassRepositorySettings' => $taxClassRepositorySettings,
            'parsedTaxes' => $taxes
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $slotReturn = $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );

        if (is_array($slotReturn[0]['cartProduct'])) {
            $parsedTaxes = $slotReturn[0]['cartProduct'];

            foreach ($parsedTaxes as $parsedTaxKey => $parsedTaxValue) {
                if ($parsedTaxValue instanceof \Extcode\Cart\Domain\Model\Cart\TaxClass) {
                    $taxes[$parsedTaxKey] = $parsedTaxValue;
                }
            }
        }

        return $taxes;
    }

    /**
     * Parse Services
     *
     * @param string $serviceType
     * @param array $pluginSettings Plugin Settings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return array
     */
    public function parseServices(
        string $serviceType,
        array $pluginSettings,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $services = [];
        $type = strtolower($serviceType) . 's';

        $pluginSettingsType = $this->getTypePluginSettings($pluginSettings, $cart, $type);

        if ($pluginSettingsType['options']) {
            foreach ($pluginSettingsType['options'] as $serviceKey => $serviceConfig) {
                $className = 'Extcode\\Cart\\Domain\\Model\\Cart\\' . $serviceType;

                if ($serviceConfig['className']) {
                    $className = $serviceConfig['className'];
                } else {
                    $className = \Extcode\Cart\Domain\Model\Cart\Service::class;
                }

                $service = $this->objectManager->get(
                    $className,
                    (int)$serviceKey,
                    $serviceConfig
                );
                if (!$service instanceof ServiceInterface) {
                    throw new \UnexpectedValueException($className . ' must implement interface ' . ServiceInterface::class, 123);
                }

                $service->setCart($cart);

                if ($pluginSettingsType['preset'] == $serviceKey) {
                    $service->setPreset(true);
                }

                $services[$serviceKey] = $service;
            }
        }

        return $services;
    }

    /**
     * @param array $pluginSettings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param string $type
     *
     * @return array
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
            if (is_array($pluginSettingsType['countries'][$selectedCountry])) {
                $countrySetting = $pluginSettingsType['countries'][$selectedCountry];
                if (is_array($countrySetting) && !empty($countrySetting)) {
                    return $countrySetting;
                }
            }

            if (is_array($pluginSettingsType['zones'])) {
                $zoneSetting = $this->getTypeZonesPluginSettings($pluginSettingsType['zones'], $cart);
                if (is_array($zoneSetting) && !empty($zoneSetting)) {
                    return $zoneSetting;
                }
            }

            if (is_array($pluginSettingsType[$selectedCountry])) {
                $countrySetting = $pluginSettingsType[$selectedCountry];
                if (is_array($countrySetting) && !empty($countrySetting)) {
                    return $countrySetting;
                }
            }

            return $pluginSettingsType;
        }
        return $pluginSettingsType;
    }

    /**
     * @param array $zoneSettings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return array
     */
    public function getTypeZonesPluginSettings(array $zoneSettings, \Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        foreach ($zoneSettings as $zoneSetting) {
            $zoneSetting['countries'] = preg_replace('/\s+/', '', $zoneSetting['countries']);
            $countriesInZones = explode(',', $zoneSetting['countries']);

            if (in_array($cart->getCountry(), $countriesInZones)) {
                return $zoneSetting;
            }
        }

        return [];
    }
}
