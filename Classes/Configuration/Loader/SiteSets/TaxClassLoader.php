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
use Extcode\Cart\Utility\ArrayUtility;
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

        $taxClassSettings = $this->getTaxClassSettings();
        if ($taxClassSettings === null) {
            return [];
        }

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

        // the site settings do not keep the order of the keys, e.g. 3, 2, 1
        ksort($taxClasses);

        return $taxClasses;
    }

    /**
     * @return null|array<array-key, mixed> the tax classes of extcode.cart.taxClasses, null if there are none
     */
    private function getTaxClassSettings(): ?array
    {
        $configuration = $this->site->getSettings()->getAll();

        $taxClassSettings = ArrayUtility::getArrayByPathWithFallback($configuration, 'extcode.cart.taxClasses', '.');
        if ($taxClassSettings !== null) {
            return $taxClassSettings;
        }

        // TODO: 13.0.0 Remove the fallback to extcode.taxclasses, see Deprecation #847.
        $taxClassSettings = ArrayUtility::getArrayByPathWithFallback($configuration, 'extcode.taxclasses', '.');
        if ($taxClassSettings !== null) {
            trigger_error(
                'The site setting "extcode.taxclasses" is deprecated since cart 12.4 and will stop working in cart 13.0.'
                . ' Use "extcode.cart.taxClasses" instead.',
                E_USER_DEPRECATED
            );

            return $taxClassSettings;
        }

        return null;
    }
}
