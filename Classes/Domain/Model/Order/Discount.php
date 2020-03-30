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
     * Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     * Title
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * Code
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $code = '';

    /**
     * Gross
     *
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $gross = 0.0;

    /**
     * Net
     *
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $net = 0.0;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $tax = 0.0;

    /**
     * __construct
     *
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
     * Returns the Order Item
     *
     * @return \Extcode\Cart\Domain\Model\Order\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Returns Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns Code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns Gross
     *
     * @return float
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * Returns Net
     *
     * @return float
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * Returns TaxClass
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Returns Tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }
}
