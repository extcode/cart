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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class TaxClassService implements TaxClassServiceInterface
{
    protected ConfigurationManagerInterface $configurationManager;

    protected array $settings;

    public function injectConfigurationManager(ConfigurationManager $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    /**
     * @inheritDoc
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
            empty($value['calc']) ||
            !is_numeric($value['calc'])
        ) {
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->error('Can\'t create tax class object for \'' . $key . '\'.', []);

            return false;
        }

        return true;
    }
}
