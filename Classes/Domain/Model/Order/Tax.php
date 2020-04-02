<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Tax extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $tax;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $taxClass;

    /**
     * @param float $tax
     * @param TaxClass $taxClass
     */
    public function __construct(
        float $tax,
        TaxClass $taxClass
    ) {
        $this->tax = $tax;
        $this->taxClass = $taxClass;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }
}
