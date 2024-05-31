<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

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

abstract class AbstractConfigurationFromTypoScriptService
{
    private array $configuration;

    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ServiceFactory $serviceFactory
    ) {
        $this->configuration = $this->configurationManager->getConfiguration(
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

            $service[$serviceKey] = $service;
        }

        return $services;
    }

    public function getConfigurationsForType(string $configurationType, ?string $country = null): ?array
    {
        var_dump($this->configuration['payments']);
        $configuration = $this->configuration[$configurationType];

        if (is_null($country)) {
            if (isset($configuration['settings']['countries']['options'][$configuration['settings']['countries']['preset']]['code'])) {
                $country = $configuration['settings']['countries']['options'][$configuration['settings']['countries']['preset']]['code'];
            }
        }

        if (isset($country)) {
            if (
                !empty($configuration['countries']) &&
                is_array($configuration['countries'][$country]) &&
                !empty($configuration['countries'][$country])
            ) {
                return $configuration['countries'][$country];
            }

            if (!empty($configuration['zones']) && is_array($configuration['zones'])) {
                $zoneSetting = $this->getTypeZonesPluginSettings($configuration['zones'], $country);
                if (!empty($zoneSetting)) {
                    return $zoneSetting;
                }
            }

            return $configuration;
        }

        return null;
    }

    protected function getTypeZonesPluginSettings(array $zoneSettings, string $country): array
    {
        foreach ($zoneSettings as $zoneSetting) {
            $countriesInZones = GeneralUtility::trimExplode(',', $zoneSetting['countries'], true);

            if (in_array($country, $countriesInZones)) {
                return $zoneSetting;
            }
        }

        return [];
    }
}
