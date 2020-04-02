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
     * @var int
     */
    protected $id;

    /**
     * @var float
     */
    protected $condition = 0.0;

    /**
     * @var float
     */
    protected $price = 0.0;

    /**
     * @var float
     */
    protected $gross = 0.0;

    /**
     * @var float
     */
    protected $net = 0.0;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * @var float
     */
    protected $tax = 0.0;

    /**
     * @var bool
     */
    protected $isNetPrice = false;

    /**
     * @var string
     */
    protected $extraType = '';

    /**
     * @param int $id
     * @param float $condition
     * @param float $price
     * @param TaxClass $taxClass
     * @param bool $isNetPrice
     * @param string $extraType
     *
     * @internal param $gross
     */
    public function __construct(
        int $id,
        float $condition,
        float $price,
        TaxClass $taxClass,
        bool $isNetPrice = false,
        string $extraType = ''
    ) {
        $this->id = $id;
        $this->condition = $condition;
        $this->taxClass = $taxClass;
        $this->price = $price;

        $this->isNetPrice = $isNetPrice;

        $this->extraType = $extraType;

        $this->reCalc();
    }

    /**
     * @param bool
     */
    public function setIsNetPrice(bool $isNetPrice)
    {
        $this->isNetPrice = $isNetPrice;
    }

    /**
     * @return bool
     */
    public function getIsNetPrice(): bool
    {
        return $this->isNetPrice;
    }

    /**
     * @return string
     */
    public function getExtraType(): string
    {
        return $this->extraType;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getCondition(): float
    {
        return $this->condition;
    }

    /**
     * @param $condition
     *
     * @return bool
     */
    public function leq($condition): bool
    {
        if ($condition < $this->condition) {
            return false;
        }

        return true;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        $this->reCalc();
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        $this->calcGross();
        return $this->gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        $this->calcNet();
        return $this->net;
    }

    /**
     * @return array
     */
    public function getTax(): array
    {
        $this->calcTax();
        return ['taxclassid' => $this->taxClass->getId(), 'tax' => $this->tax];
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    protected function calcGross()
    {
        if ($this->isNetPrice == false) {
            $this->gross = $this->price;
        } else {
            $this->calcNet();
            $this->gross = $this->net + $this->tax;
        }
    }

    protected function calcTax()
    {
        if ($this->isNetPrice == false) {
            $this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
        } else {
            $this->tax = ($this->net * $this->taxClass->getCalc());
        }
    }

    protected function calcNet()
    {
        if ($this->isNetPrice == true) {
            $this->net = $this->price;
        } else {
            $this->calcGross();
            $this->net = $this->gross - $this->tax;
        }
    }

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
