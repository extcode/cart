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

final class TaxClassFactory implements TaxClassFactoryInterface
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function getTaxClass(int $taxClassKey, array $taxClassValue): ?TaxClass
    {
        if ($this->isValidTaxClassConfig($taxClassKey, $taxClassValue)) {
            return new TaxClass(
                $taxClassKey,
                $taxClassValue['value'],
                (float)$taxClassValue['calc'],
                $taxClassValue['name']
            );
        }

        return null;
    }

    private function isValidTaxClassConfig(int $key, array $value): bool
    {
        if (empty($value) ||
            empty($value['name']) ||
            !isset($value['calc']) ||
            !is_numeric($value['calc'])
        ) {
            $this->logger->error('Can\'t create tax class object for the configuration with the index=' . $key . '.', []);

            return false;
        }

        return true;
    }
}
