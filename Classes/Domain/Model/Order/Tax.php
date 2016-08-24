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
 * Order Tax Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Tax extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Tax
     *
     * @var float
     * @validate NotEmpty
     */
    protected $tax;

    /**
     * TaxClass
     *
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     * @validate NotEmpty
     */
    protected $taxClass;

    /**
     * __construct
     *
     * @param float $tax
     * @param \Extcode\Cart\Domain\Model\Order\TaxClass $taxClass
     *
     * @return \Extcode\Cart\Domain\Model\Order\Tax
     */
    public function __construct(
        $tax,
        $taxClass
    ) {
        if (!isset($tax)) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $tax for constructor.',
                1456836510
            );
        }
        if (!$taxClass) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $taxClass for constructor.',
                1456836520
            );
        }

        $this->tax = $tax;
        $this->taxClass = $taxClass;
    }

    /**
     * Gets Tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Gets Tax Class
     *
     * @return \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }
}
