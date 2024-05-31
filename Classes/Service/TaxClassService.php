<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class TaxClassService implements TaxClassServiceInterface
{
    protected array $settings;

    public function __construct(
        protected ConfigurationManagerInterface $configurationManager
    ) {
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    /**
     * @return TaxClass[]
     */
    public function getTaxClasses(string $countryCode = null): array
    {
        $taxClasses = [];
        $taxClassSettings = $this->settings['taxClasses'];

        if (
            array_key_exists($countryCode, $taxClassSettings) &&
            is_array($taxClassSettings[$countryCode])
        ) {
            $taxClassSettings = $taxClassSettings[$countryCode];
        } elseif (
            array_key_exists('fallback', $taxClassSettings) &&
            is_array($taxClassSettings['fallback'])
        ) {
            $taxClassSettings = $taxClassSettings['fallback'];
        }

        foreach ($taxClassSettings as $taxClassKey => $taxClassValue) {
            if ($this->isValidTaxClassConfig($taxClassKey, $taxClassValue)) {
                $taxClasses[$taxClassKey] = GeneralUtility::makeInstance(
                    TaxClass::class,
                    (int)$taxClassKey,
                    $taxClassValue['value'],
                    (float)$taxClassValue['calc'],
                    $taxClassValue['name']
                );
            }
        }

        return $taxClasses;
    }

    protected function isValidTaxClassConfig(int $key, array $value): bool
    {
        if (empty($value) ||
            empty($value['name']) ||
            !isset($value['calc']) ||
            (isset($value['calc']) && !is_numeric($value['calc']))
        ) {
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->error('Can\'t create tax class object for the configuration with the index=' . $key . '.', []);

            return false;
        }

        return true;
    }
}
