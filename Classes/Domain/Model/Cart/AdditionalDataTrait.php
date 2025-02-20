<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

trait AdditionalDataTrait
{
    /**
     * @var array<string, mixed>
     */
    private array $additionals = [];

    /**
     * @return array<string, mixed>
     */
    public function getAdditionals(): array
    {
        return $this->additionals;
    }

    /**
     * @param array<string, mixed> $additionals
     */
    public function setAdditionals(array $additionals): void
    {
        $this->additionals = $additionals;
    }

    public function unsetAdditionals(): void
    {
        $this->additionals = [];
    }

    public function getAdditional(string $key): mixed
    {
        return $this->additionals[$key];
    }

    public function setAdditional(string $key, mixed $value): void
    {
        $this->additionals[$key] = $value;
    }

    public function unsetAdditional(string $key): void
    {
        if (isset($this->additionals[$key])) {
            unset($this->additionals[$key]);
        }
    }
}
