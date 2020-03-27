<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Extra
{
    /**
     * Id
     *
     * @var int
     */
    protected $id;

    /**
     * Condition
     *
     * @var float
     */
    protected $condition;

    /**
     * Price
     *
     * @var float
     */
    protected $price;

    /**
     * Gross
     *
     * @var float
     */
    protected $gross;

    /**
     * Net
     *
     * @var float
     */
    protected $net;

    /**
     * TaxClass
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax;

    /**
     * Is Net Price
     *
     * @var bool
     */
    protected $isNetPrice;

    /**
     * __construct
     *
     * @param int $id
     * @param float $condition
     * @param float $price
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     * @param bool $isNetPrice
     *
     * @internal param $gross
     */
    public function __construct(
        $id,
        $condition,
        $price,
        \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass,
        $isNetPrice = false
    ) {
        $this->id = $id;
        $this->condition = $condition;
        $this->taxClass = $taxClass;
        $this->price = $price;

        $this->isNetPrice = $isNetPrice;

        $this->reCalc();
    }

    /**
     * Sets Is Net Price
     *
     * @param bool
     */
    public function setIsNetPrice($isNetPrice)
    {
        $this->isNetPrice = $isNetPrice;
    }

    /**
     * Gets Is Net Price
     *
     * @return bool
     */
    public function getIsNetPrice()
    {
        return $this->isNetPrice;
    }

    /**
     * Gets Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets Condition
     *
     * @return float
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Less Or Equal
     *
     * @param $condition
     *
     * @return bool
     */
    public function leq($condition)
    {
        if ($condition < $this->condition) {
            return false;
        }

        return true;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param $price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        $this->reCalc();
    }

    /**
     * @return float
     */
    public function getGross()
    {
        $this->calcGross();
        return $this->gross;
    }

    /**
     * @return float
     */
    public function getNet()
    {
        $this->calcNet();
        return $this->net;
    }

    /**
     * @return array
     */
    public function getTax()
    {
        $this->calcTax();
        return ['taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax];
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     *
     */
    protected function calcGross()
    {
        if ($this->isNetPrice == false) {
            $this->gross = $this->price;
        } else {
            $this->calcNet();
            $this->gross = $this->net + $this->tax;
        }
    }

    /**
     *
     */
    protected function calcTax()
    {
        if ($this->isNetPrice == false) {
            $this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
        } else {
            $this->tax = ($this->net * $this->taxClass->getCalc());
        }
    }

    /**
     *
     */
    protected function calcNet()
    {
        if ($this->isNetPrice == true) {
            $this->net = $this->price;
        } else {
            $this->calcGross();
            $this->net = $this->gross - $this->tax;
        }
    }

    /**
     *
     */
    protected function reCalc()
    {
        if ($this->isNetPrice == false) {
            $this->calcGross();
            $this->calcTax();
            $this->calcNet();
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->calcGross();
        }
    }
}
