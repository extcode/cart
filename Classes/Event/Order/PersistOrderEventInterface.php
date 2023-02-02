<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;

interface PersistOrderEventInterface extends EventInterface
{
    public function setSettings(array $settings): void;

    public function getStoragePid(): int;

    public function getTaxClasses(): array;

    public function setTaxClasses(array $taxClasses): void;
}
