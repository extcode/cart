<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;

class Discount extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $code = '';

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $gross = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $net = 0.0;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $taxClass;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $tax = 0.0;

    /**
     * @param string $title
     * @param string $code
     * @param float $gross
     * @param float $net
     * @param TaxClass $taxClass
     * @param float $tax
     */
    public function __construct(
        string $title,
        string $code,
        float $gross,
        float $net,
        TaxClass $taxClass,
        float $tax
    ) {
        $this->title = $title;
        $this->code = $code;
        $this->gross = $gross;
        $this->net = $net;
        $this->taxClass = $taxClass;
        $this->tax = $tax;
    }

    /**
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @return TaxClass|null
     */
    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }
}
