<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class FeVariant
{
    protected ?Product $product = null;

    protected ?BeVariant $beVariant = null;

    protected array $variantData = [];

    protected string $titleGlue = ' ';

    protected string $skuGlue = '-';

    protected string $valueGlue = ' ';

    public function __construct(
        array $variantData = []
    ) {
        $this->variantData = $variantData;
    }

    public function getId(): string
    {
        return sha1(json_encode($this->variantData));
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getVariant(): ?BeVariant
    {
        return $this->beVariant;
    }

    public function getVariantData(): array
    {
        return $this->variantData;
    }

    public function getTitle(): string
    {
        $titleArr = [];
        foreach ($this->variantData as $variant) {
            $titleArr[] = $variant['title'];
        }
        return implode($this->titleGlue, $titleArr);
    }

    public function getSku(): string
    {
        $skuArr = [];
        foreach ($this->variantData as $variant) {
            $skuArr[] = $variant['sku'];
        }
        return implode($this->skuGlue, $skuArr);
    }

    public function getValue(): string
    {
        $valueArr = [];
        foreach ($this->variantData as $variant) {
            $valueArr[] = $variant['value'];
        }
        return implode($this->valueGlue, $valueArr);
    }
}
