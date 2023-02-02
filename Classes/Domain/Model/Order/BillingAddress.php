<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class BillingAddress extends AbstractAddress
{
    protected string $taxIdentificationNumber = '';

    public function getTaxIdentificationNumber(): string
    {
        return $this->taxIdentificationNumber;
    }

    public function setTaxIdentificationNumber(string $taxIdentificationNumber): void
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;
    }
}
