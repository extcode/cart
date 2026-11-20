<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Log\LoggerInterface;

final readonly class TaxClassFactory implements TaxClassFactoryInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    /**
     * @param array<array-key, mixed> $taxClassValue the configuration with "value", "calc" and "name", strings of
     *                                               TypoScript or typed values of site settings (e.g. "value: 19")
     */
    public function getTaxClass(int $taxClassKey, array $taxClassValue): ?TaxClass
    {
        $value = $taxClassValue['value'] ?? null;
        $calc = $taxClassValue['calc'] ?? null;
        $name = $taxClassValue['name'] ?? null;

        if (!is_scalar($value)
            || !is_numeric($calc)
            || !is_scalar($name)
            || empty($name)
        ) {
            $this->logger->error('Can\'t create tax class object for the configuration with the index=' . $taxClassKey . '.', []);

            return null;
        }

        return new TaxClass(
            $taxClassKey,
            (string) $value,
            (float) $calc,
            (string) $name
        );
    }
}
