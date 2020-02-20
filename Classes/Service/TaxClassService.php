<?php
declare(strict_types = 1);

namespace Extcode\Cart\Service;

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class TaxClassService implements TaxClassServiceInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'Cart'
        );
    }

    /**
     * @inheritDoc
     */
    public function getTaxClasses(string $countryCode): array
    {
        $taxClasses = [];
        $taxClassSettings = $this->settings['taxClasses'];

        if ($countryCode && is_array($taxClassSettings[$countryCode])) {
            $taxClassSettings = $taxClassSettings[$countryCode];
        } elseif ($taxClassSettings['fallback'] && is_array($taxClassSettings['fallback'])) {
            $taxClassSettings = $taxClassSettings['fallback'];
        }

        foreach ($taxClassSettings as $taxClassKey => $taxClassValue) {
            if ($this->isValidTaxClassConfig($taxClassKey, $taxClassValue)) {
                $taxClasses[$taxClassKey] = $this->objectManager->get(
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

    /**
     * @param int $key
     * @param array $value
     * @return bool
     */
    protected function isValidTaxClassConfig(int $key, array $value): bool
    {
        if ((empty($value) && !is_numeric($value)) ||
            empty($value['name']) ||
            empty($value['calc']) ||
            !is_numeric($value['calc'])
        ) {
            $logger = $this->objectManager->get(LogManager::class)->getLogger(__CLASS__);
            $logger->error('Can\'t create tax class object for \'' . $key . '\'.', []);

            return false;
        }

        return true;
    }
}
