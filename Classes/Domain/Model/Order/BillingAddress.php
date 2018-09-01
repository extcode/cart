<?php

namespace Extcode\Cart\Domain\Model\Order;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Order Billing Address Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BillingAddress extends \Extcode\Cart\Domain\Model\Order\AbstractAddress
{
    /**
     * @var string
     */
    protected $taxIdentificationNumber;

    /**
     * @return string
     */
    public function getTaxIdentificationNumber()
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     */
    public function setTaxIdentificationNumber($taxIdentificationNumber)
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;
    }
}
