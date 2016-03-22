<?php

namespace Extcode\Cart\Domain\Model\Cart;

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
 * Cart Extra Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Extra
{
    /**
     * Id
     *
     * @var int
     */
    private $id;

    /**
     * Condition
     *
     * @var float
     */
    private $condition;

    /**
     * Price
     *
     * @var float
     */
    private $price;

    /**
     * Gross
     *
     * @var float
     */
    private $gross;

    /**
     * Net
     *
     * @var float
     */
    private $net;

    /**
     * TaxClass
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    private $taxClass;

    /**
     * Tax
     *
     * @var float
     */
    private $tax;

    /**
     * Is Net Price
     *
     * @var bool
     */
    private $isNetPrice;

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
     *
     * @return Extra
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
     *
     * @return void
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
        return array('taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax);
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
    private function calcGross()
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
    private function calcTax()
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
    private function calcNet()
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
    private function reCalc()
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
