<?php

namespace Extcode\Cart\Domain\Model\Cart;

interface FeVariantWithPriceInterface extends FeVariantInterface
{
    public function getPrice(): float;
}
