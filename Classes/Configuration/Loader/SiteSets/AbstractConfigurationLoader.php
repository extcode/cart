<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\SiteSets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\ServiceFactory;
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;
use Extcode\Cart\Utility\ArrayUtility;
use Extcode\Cart\Utility\Typo3GlobalsUtility;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

abstract readonly class AbstractConfigurationLoader
{
    private Site $site;

    public function __construct(
        private ServiceFactory $serviceFactory
    ) {
        $this->site = Typo3GlobalsUtility::getSiteFromTypo3Request();
    }

    /**
     * @param array<int, ServiceInterface> $services
     *
     * @return array<int, ServiceInterface>
     */
    public function getServices(?array $configurations, array $services, Cart $cart): array
    {
        if (is_array($configurations['options'] ?? null) === false) {
            return $services;
        }

        foreach ($configurations['options'] as $serviceKey => $serviceConfig) {
            if (is_numeric($serviceKey) === false
                || is_array($serviceConfig) === false
            ) {
                continue;
            }

            $service = $this->serviceFactory->getService(
                (int) $serviceKey,
                $serviceConfig,
                $configurations['preset'] == $serviceKey
            );
            $service->setCart($cart);

            $services[$serviceKey] = $service;
        }

        return $services;
    }

    /**
     * @param string $configurationType the path below "extcode" of the site settings, e.g. "cart.payments"
     */
    public function getConfigurationsForType(string $configurationType, ?string $country = null): ?array
    {
        $configuration = $this->getSettingsByPath('extcode.' . $configurationType);
        if ($configuration === null) {
            return null;
        }

        if (is_null($country)) {
            $country = self::getCountryCodeFromPreset($configuration);
        }
        if (is_null($country)) {
            return null;
        }

        if (is_array($configuration['countries'] ?? null)
            && is_array($configuration['countries'][$country] ?? null)
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

    /**
     * Returns the configuration type to read, the deprecated one only if the
     * site has settings there but none for the configuration type.
     *
     * TODO: 13.0.0 Remove with the deprecated configuration types, see Deprecation #848.
     *
     * @param string $configurationType e.g. "cart.payments"
     * @param string $deprecatedConfigurationType e.g. "cart-payments"
     */
    protected function resolveConfigurationType(string $configurationType, string $deprecatedConfigurationType): string
    {
        if ($this->getSettingsByPath('extcode.' . $configurationType) !== null
            || $this->getSettingsByPath('extcode.' . $deprecatedConfigurationType) === null
        ) {
            return $configurationType;
        }

        trigger_error(
            'The site setting "extcode.' . $deprecatedConfigurationType . '" is deprecated since cart 12.4 and will stop working in cart 13.0.'
            . ' Use "extcode.' . $configurationType . '" instead.',
            E_USER_DEPRECATED
        );

        return $deprecatedConfigurationType;
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

    /**
     * @param string $path e.g. "extcode.cart.payments"
     *
     * @return null|array<array-key, mixed> null if the path does not exist in the site settings or is no array
     */
    private function getSettingsByPath(string $path): ?array
    {
        return ArrayUtility::getArrayByPathWithFallback($this->site->getSettings()->getAll(), $path, '.');
    }
}
