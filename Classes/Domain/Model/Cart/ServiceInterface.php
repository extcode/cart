<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface ServiceInterface
{
    public function __construct(int $id, array $config);

    public function getId(): int;
    public function getFallBackId(): ?int;

    public function getConfig(): array;

    public function setCart(Cart $cart);

    public function getCart(): Cart;

    public function setPreset(bool $preset);

    public function isPreset(): bool;

    public function isAvailable(): bool;

    public function isFree(): bool;

    public function getStatus(): string;

    public function getProvider(): string;

    public function getName(): string;

    public function getNet(): float;

    public function getGross(): float;

    public function getTax(): float;

    public function getTaxes(): array;

    public function getTaxClass(): TaxClass;
}
