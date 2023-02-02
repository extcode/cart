<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;
use Extcode\Cart\Service\TaxClassService;
use Extcode\Cart\Service\TaxClassServiceInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ParserUtility
{
    public function parseTaxClasses(array $pluginSettings, string $countryCode = null): array
    {
        $className = $pluginSettings['taxClasses']['className'] ?? TaxClassService::class;

        $service = GeneralUtility::makeInstance(
            $className
        );
        if (!$service instanceof TaxClassServiceInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . TaxClassServiceInterface::class, 123);
        }

        return $service->getTaxClasses($countryCode);
    }

    /**
     * Parse Services
     *
     * @param string $serviceType
     * @param array $pluginSettings Plugin Settings
     * @param Cart $cart
     *
     * @return array
     */
    public function parseServices(
        string $serviceType,
        array $pluginSettings,
        Cart $cart
    ) {
        $services = [];
        $type = strtolower($serviceType) . 's';

        $pluginSettingsType = $this->getTypePluginSettings($pluginSettings, $cart, $type);

        if (
            !isset($pluginSettingsType['options']) ||
            empty($pluginSettingsType['options'])
        ) {
            return $services;
        }

        foreach ($pluginSettingsType['options'] as $serviceKey => $serviceConfig) {
            if (!empty($serviceConfig['className'])) {
                $className = $serviceConfig['className'];
            } else {
                $className = Service::class;
            }

            $service = GeneralUtility::makeInstance(
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

        return $services;
    }

    /**
     * @param array $pluginSettings
     * @param Cart $cart
     * @param string $type
     *
     * @return array
     */
    public function getTypePluginSettings(array $pluginSettings, Cart $cart, string $type): ?array
    {
        if (
            !isset($pluginSettings[$type]) ||
            empty($pluginSettings[$type])
        ) {
            return null;
        }
        $pluginSettingsType = $pluginSettings[$type];
        $selectedCountry = $pluginSettings['settings']['defaultCountry'];

        if ($cart->getCountry()) {
            if ($type === 'payments') {
                $selectedCountry = $cart->getBillingCountry();
            } else {
                $selectedCountry = $cart->getCountry();
            }
        }

        if ($selectedCountry) {
            if (!empty($pluginSettingsType['countries']) && is_array($pluginSettingsType['countries'][$selectedCountry])) {
                $countrySetting = $pluginSettingsType['countries'][$selectedCountry];
                if (is_array($countrySetting) && !empty($countrySetting)) {
                    return $countrySetting;
                }
            }

            if (!empty($pluginSettingsType['zones']) && is_array($pluginSettingsType['zones'])) {
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
     * @param Cart $cart
     *
     * @return array
     */
    public function getTypeZonesPluginSettings(array $zoneSettings, Cart $cart)
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
