<?php

namespace Extcode\Cart\Domain\Model\Cart;

interface FeVariantInterface
{
    public function getId(): string;

    public function getVariantData(): array;

    public function getTitle(): string;

    public function getSku(): string;

    public function getValue(): string;
}
