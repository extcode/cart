<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class FeVariant
{
    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected $beVariant = null;

    /**
     * @var array
     */
    protected $variantData = [];

    /**
     * @var string
     */
    protected $titleGlue = ' ';

    /**
     * @var string
     */
    protected $skuGlue = '-';

    /**
     * @var string
     */
    protected $valueGlue = ' ';

    /**
     * @param array $variantData
     */
    public function __construct(
        array $variantData = []
    ) {
        $this->variantData = $variantData;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return sha1(json_encode($this->variantData));
    }

    /**
     * @return Product
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @return BeVariant
     */
    public function getVariant(): ?BeVariant
    {
        return $this->beVariant;
    }

    /**
     * @return array
     */
    public function getVariantData(): array
    {
        return $this->variantData;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        $titleArr = [];
        foreach ($this->variantData as $variant) {
            $titleArr[] = $variant['title'];
        }
        return implode($this->titleGlue, $titleArr);
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        $skuArr = [];
        foreach ($this->variantData as $variant) {
            $skuArr[] = $variant['sku'];
        }
        return implode($this->skuGlue, $skuArr);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        $valueArr = [];
        foreach ($this->variantData as $variant) {
            $valueArr[] = $variant['value'];
        }
        return implode($this->valueGlue, $valueArr);
    }
}
