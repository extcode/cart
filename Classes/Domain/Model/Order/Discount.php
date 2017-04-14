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
 * Order Discount Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
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
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * Code
     *
     * @var string
     * @validate NotEmpty
     */
    protected $code = '';

    /**
     * Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $gross = 0.0;

    /**
     * Net
     *
     * @var float
     * @validate NotEmpty
     */
    protected $net = 0.0;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     * @validate NotEmpty
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     * @validate NotEmpty
     */
    protected $tax = 0.0;

    /**
     * __construct
     *
     * @param string $title
     * @param string $code
     * @param float $discount
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     * @param float $tax
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $title,
        $code,
        $gross,
        $net,
        $taxClass,
        $tax
    ) {
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1455452810
            );
        }
        if (!$code) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $code for constructor.',
                1455452820
            );
        }
        if (!$gross) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $gross for constructor.',
                1468779204
            );
        }
        if (!$net) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $net for constructor.',
                1468779221
            );
        }
        if (!$taxClass) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $taxClass for constructor.',
                1455452840
            );
        }
        if (!$tax) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $tax for constructor.',
                1455452850
            );
        }

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
