<?php
declare(strict_types = 1);

namespace Extcode\Cart\Domain\Model\Cart;

interface ServiceInterface
{
    /**
     * @param int $id
     * @param array $config
     */
    public function __construct(int $id, array $config);

    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return array
     */
    public function getConfig(): array;

    /**
     * @param Cart $cart
     */
    public function setCart(Cart $cart);

    /**
     * @param bool $preset
     */
    public function setPreset(bool $preset);

    /**
     * @return bool
     */
    public function isPreset(): bool;

    /**
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * @return bool
     */
    public function isFree(): bool;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return float
     */
    public function getNet(): float;

    /**
     * @return float
     */
    public function getGross(): float;

    /**
     * @return float
     */
    public function getTax(): float;

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass;
}
