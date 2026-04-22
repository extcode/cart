<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\TypoScript;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\ServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

abstract readonly class AbstractConfigurationLoader
{
    protected array $configurations;

    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
        private ServiceFactory $serviceFactory
    ) {
        $this->configurations = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    public function getServices(?array $configurations, array $services, Cart $cart): array
    {
        if (empty($configurations['options'])) {
            return $services;
        }

        foreach ($configurations['options'] as $serviceKey => $serviceConfig) {
            $service = $this->serviceFactory->getService($serviceKey, $serviceConfig, $configurations['preset'] == $serviceKey);
            $service->setCart($cart);

            $services[$serviceKey] = $service;
        }

        return $services;
    }

    public function getConfigurationsForType(string $configurationType, ?string $country = null): ?array
    {
        if (is_array($this->configurations[$configurationType]) === false
        ) {
            return null;
        }

        $configuration = $this->configurations[$configurationType];

        if (is_null($country)) {
            $country = self::getCountryCodeFromPreset($configuration);
        }
        if (is_null($country)) {
            return null;
        }

        if (!empty($configuration['countries'])
            && is_array($configuration['countries'])
            && is_array($configuration['countries'][$country])
        ) {
            return $configuration['countries'][$country];
        }

        if (!empty($configuration['zones'])
            && is_array($configuration['zones'])
        ) {
            $zoneSetting = $this->getTypeZonesPluginSettings($configuration['zones'], $country);
            if (!empty($zoneSetting)) {
                return $zoneSetting;
            }
        }

        return $configuration;
    }

    protected function getTypeZonesPluginSettings(array $zoneSettings, string $country): array
    {
        foreach ($zoneSettings as $zoneSetting) {
            if (is_array($zoneSetting) === false
                || is_string($zoneSetting['countries']) === false
            ) {
                continue;
            }
            $countriesInZones = GeneralUtility::trimExplode(',', $zoneSetting['countries'], true);

            if (in_array($country, $countriesInZones)) {
                return $zoneSetting;
            }
        }

        return [];
    }

    private static function getCountryCodeFromPreset(array $configuration): ?string
    {
        if (is_array($configuration['settings']) === false
            || is_array($configuration['settings']['countries']) === false
            || is_numeric($configuration['settings']['countries']['preset']) === false
        ) {
            return null;
        }

        $preset = (int) $configuration['settings']['countries']['preset'];

        if (is_array($configuration['settings']['countries']['options']) === false
            || is_array($configuration['settings']['countries']['options'][$preset]) === false
            || is_string($configuration['settings']['countries']['options'][$preset]['code']) === false
        ) {
            return null;
        }

        return $configuration['settings']['countries']['options'][$preset]['code'];
    }
}
