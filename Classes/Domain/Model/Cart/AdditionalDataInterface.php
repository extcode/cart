<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface AdditionalDataInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getAdditionals(): array;

    /**
     * @param array<string, mixed> $additional
     */
    public function setAdditionals(array $additional): void;

    public function unsetAdditionals(): void;

    public function getAdditional(string $key): mixed;

    public function setAdditional(string $key, mixed $value): void;

    public function unsetAdditional(string $key): void;
}
