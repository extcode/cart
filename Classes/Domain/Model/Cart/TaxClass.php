<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final class TaxClass implements TaxClassInterface
{
    public function __construct(
        private readonly int $id,
        private readonly string $value,
        private readonly float $calc,
        private readonly string $title
    ) {}

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
