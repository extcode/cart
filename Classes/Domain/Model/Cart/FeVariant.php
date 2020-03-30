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
     * Product
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * BeVariant
     *
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected $beVariant = null;

    /**
     * Variant Data
     *
     * @var array
     */
    protected $variantData = [];

    /**
     * Title Glue
     */
    protected $titleGlue = ' ';

    /**
     * SKU Glue
     */
    protected $skuGlue = '-';

    /**
     * Value Glue
     */
    protected $valueGlue = ' ';

    /**
     * __construct
     *
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
    public function getId()
    {
        return sha1(json_encode($this->variantData));
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * @return array
     */
    public function getVariantData()
    {
        return $this->variantData;
    }

    /**
     * @return string
     */
    public function getTitle()
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
    public function getSku()
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
    public function getValue()
    {
        $valueArr = [];
        foreach ($this->variantData as $variant) {
            $valueArr[] = $variant['value'];
        }
        return implode($this->valueGlue, $valueArr);
    }
}
