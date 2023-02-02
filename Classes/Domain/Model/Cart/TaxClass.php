<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class TaxClass
{
    protected int $id;

    protected string $value;

    protected float $calc;

    protected string $title;

    public function __construct(
        int $id,
        string $value,
        float $calc,
        string $title
    ) {
        $this->id = $id;
        $this->value = $value;
        $this->calc = $calc;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCalc(): float
    {
        return $this->calc;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
