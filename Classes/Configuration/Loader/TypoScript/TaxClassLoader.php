<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\TypoScript;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\TaxClassLoaderInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Domain\Model\Cart\TaxClassFactoryInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClassInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

final readonly class TaxClassLoader implements TaxClassLoaderInterface
{
    private array $settings;

    public function __construct(
        private ConfigurationManagerInterface $configurationManager,
        private TaxClassFactoryInterface $taxClassFactory
    ) {
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    /**
     * @return TaxClass[]
     */
    public function getTaxClasses(?string $countryCode = null): array
    {
        $taxClasses = [];
        $taxClassSettings = $this->settings['taxClasses'];

        if (
            is_string($countryCode)
                && array_key_exists($countryCode, $taxClassSettings)
                && is_array($taxClassSettings[$countryCode])
        ) {
            $taxClassSettings = $taxClassSettings[$countryCode];
        } elseif (
            array_key_exists('fallback', $taxClassSettings)
            && is_array($taxClassSettings['fallback'])
        ) {
            $taxClassSettings = $taxClassSettings['fallback'];
        }

        foreach ($taxClassSettings as $taxClassKey => $taxClassValue) {
            $taxClass = $this->taxClassFactory->getTaxClass($taxClassKey, $taxClassValue);

            if ($taxClass instanceof TaxClassInterface) {
                $taxClasses[$taxClassKey] = $taxClass;
            }
        }

        return $taxClasses;
    }
}
