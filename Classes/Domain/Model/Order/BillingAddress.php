<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class BillingAddress extends \Extcode\Cart\Domain\Model\Order\AbstractAddress
{
    /**
     * @var string
     */
    protected $taxIdentificationNumber = '';

    /**
     * @return string
     */
    public function getTaxIdentificationNumber(): string
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     */
    public function setTaxIdentificationNumber(string $taxIdentificationNumber)
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;
    }
}
