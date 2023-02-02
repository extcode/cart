<?php

declare(strict_types=1);

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
     * @var ServiceInterface
     */
    protected $service;

    protected int $id;

    protected float $condition = 0.0;

    protected float $price = 0.0;

    protected float $gross = 0.0;

    protected float $net = 0.0;

    protected TaxClass $taxClass;

    protected float $tax = 0.0;

    protected bool $isNetPrice = false;

    protected string $extraType = '';

    public function __construct(
        int $id,
        float $condition,
        float $price,
        TaxClass $taxClass,
        bool $isNetPrice = false,
        string $extraType = '',
        Service $service = null
    ) {
        $this->id = $id;
        $this->condition = $condition;
        $this->taxClass = $taxClass;
        $this->price = $price;

        $this->isNetPrice = $isNetPrice;

        $this->extraType = $extraType;

        $this->service = $service;

        $this->reCalc();
    }

    public function getService(): ServiceInterface
    {
        return $this->service;
    }

    public function setIsNetPrice(bool $isNetPrice): void
    {
        $this->isNetPrice = $isNetPrice;
    }

    public function isNetPrice(): bool
    {
        return $this->isNetPrice;
    }

    public function getExtraType(): string
    {
        return $this->extraType;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCondition(): float
    {
        return $this->condition;
    }

    public function leq(float $condition): bool
    {
        if ($condition < $this->condition) {
            return false;
        }

        return true;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;

        $this->reCalc();
    }

    public function getGross(): float
    {
        $this->calcGross();
        return $this->gross;
    }

    public function getNet(): float
    {
        $this->calcNet();
        return $this->net;
    }

    public function getTax(): array
    {
        $this->calcTax();
        return ['taxClassId' => $this->taxClass->getId(), 'tax' => $this->tax];
    }

    public function getTaxForTaxClass(TaxClass $taxClass): float
    {
        if ($this->service->getTaxClass()->getId() > 0 && $this->service->getTaxClass()->getId() !== $taxClass->getId()) {
            return 0.0;
        }

        if ($this->isNetPrice) {
            if ($this->service->getTaxClass()->getId() > 0) {
                return $this->net * $this->taxClass->getCalc();
            }

            if ($this->service->getTaxClass()->getId() === -2) {
                $taxClassDistribution = $this->getTaxClassDistributionOverCart();
                $factor = $taxClass->getCalc();
                return ($this->net * $taxClassDistribution[$taxClass->getId()]) * $factor;
            }
        } else {
            if ($this->service->getTaxClass()->getId() > 0) {
                return ($this->gross / (1 + $this->taxClass->getCalc())) * $this->taxClass->getCalc();
            }

            if ($this->service->getTaxClass()->getId() === -2) {
                $taxClassDistribution = $this->getTaxClassDistributionOverCart();
                $factor = $taxClass->getCalc();
                return ($this->gross * $taxClassDistribution[$taxClass->getId()]) / (1 + $factor) * $factor;
            }
        }

        return 0.0;
    }

    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    protected function calcGross(): void
    {
        if ($this->isNetPrice) {
            $this->calcNet();
            $this->gross = $this->net + $this->tax;
        } else {
            $this->gross = $this->price;
        }
    }

    protected function calcTax(): void
    {
        if ($this->isNetPrice) {
            if ($this->service->getTaxClass()->getId() > 0) {
                $this->tax = ($this->net * $this->taxClass->getCalc());
            } elseif ($this->service->getTaxClass()->getId() === -2) {
                $tax = 0.0;
                foreach ($this->getTaxClassDistributionOverCart() as $taxClassId => $taxClassDistribution) {
                    $factor = $this->service->getCart()->getTaxClass($taxClassId)->getCalc();
                    $tax += ($this->net * $taxClassDistribution) * $factor;
                }
                $this->tax = $tax;
            }
        } else {
            if ($this->service->getTaxClass()->getId() > 0) {
                $this->tax = ($this->gross / (1 + $this->taxClass->getCalc())) * $this->taxClass->getCalc();
            } elseif ($this->service->getTaxClass()->getId() === -2) {
                $tax = 0.0;
                foreach ($this->getTaxClassDistributionOverCart() as $taxClassId => $taxClassDistribution) {
                    $factor = $this->service->getCart()->getTaxClass($taxClassId)->getCalc();
                    $tax += ($this->gross * $taxClassDistribution) / (1 + $factor) * $factor;
                }
                $this->tax = $tax;
            }
        }
    }

    protected function getTaxClassDistributionOverCart(): array
    {
        $taxClassDistribution = [];

        foreach ($this->service->getCart()->getTaxClasses() as $taxClass) {
            $taxClassDistribution[$taxClass->getId()] = 0.0;
        }

        foreach ($this->service->getCart()->getProducts() as $product) {
            $taxClassDistribution[$product->getTaxClass()->getId()] += $product->getGross();
        }

        $total = array_sum($taxClassDistribution);

        return array_map(function ($gross) use ($total) {
            return $gross / $total;
        }, $taxClassDistribution);
    }

    protected function calcNet(): void
    {
        if ($this->isNetPrice) {
            $this->net = $this->price;
        } else {
            $this->calcGross();
            $this->net = $this->gross - $this->tax;
        }
    }

    protected function reCalc(): void
    {
        if ($this->isNetPrice) {
            $this->calcNet();
            $this->calcTax();
            $this->calcGross();
        } else {
            $this->calcGross();
            $this->calcTax();
            $this->calcNet();
        }
    }
}
