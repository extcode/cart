<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

trait StockTrait
{
    protected bool $handleStock = false;

    protected int $stock = 0;

    public function isHandleStock(): bool
    {
        return $this->handleStock;
    }

    public function setIsHandleStock(bool $handleStock): void
    {
        $this->handleStock = $handleStock;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
}
