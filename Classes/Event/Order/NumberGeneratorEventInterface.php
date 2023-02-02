<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface NumberGeneratorEventInterface extends EventInterface
{
    public function getOnlyGenerateNumberOfType(): array;

    public function setOnlyGenerateNumberOfType(array $onlyGenerateNumberOfType): void;
}
