<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\SiteSets;

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
use Extcode\Cart\Utility\Typo3GlobalsUtility;
use TYPO3\CMS\Core\Site\Entity\Site;

final readonly class TaxClassLoader implements TaxClassLoaderInterface
{
    private Site $site;

    public function __construct(
        private TaxClassFactoryInterface $taxClassFactory,
    ) {
        $this->site = Typo3GlobalsUtility::getSiteFromTypo3Request();
    }

    /**
     * @return TaxClass[]
     */
    public function getTaxClasses(?string $countryCode = null): array
    {
        $taxClasses = [];

        $configuration = $this->site->getSettings()->getAll();
        if (is_array($configuration['extcode']) === false
            || is_array($configuration['extcode']['taxclasses']) === false
        ) {
            return [];
        }

        $taxClassSettings = $configuration['extcode']['taxclasses'];
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
            if (is_array($taxClassValue) === false) {
                continue;
            }
            $taxClass = $this->taxClassFactory->getTaxClass($taxClassKey, $taxClassValue);

            if ($taxClass instanceof TaxClassInterface) {
                $taxClasses[$taxClassKey] = $taxClass;
            }
        }

        return $taxClasses;
    }
}
