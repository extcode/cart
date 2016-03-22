<?php

namespace Extcode\Cart\Domain\Model\Cart;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cart FeVariant Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class FeVariant
{
    /**
     * Product
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    private $product = null;

    /**
     * BeVariant
     *
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    private $beVariant = null;

    /**
     * Variant Data
     *
     * @var array
     */
    private $variantData = array();

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
     *
     * @return \Extcode\Cart\Domain\Model\Cart\FeVariant
     */
    public function __construct(
        $variantData = array()
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
    public function getVariantData() {
        return $this->variantData;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $titleArr = array();
        foreach ($this->variantData as $variant) {
            $titleArr[] = $variant['title'];
        }
        return join($this->titleGlue, $titleArr);
    }

    /**
     * @return string
     */
    public function getSku()
    {
        $skuArr = array();
        foreach ($this->variantData as $variant) {
            $skuArr[] = $variant['sku'];
        }
        return join($this->skuGlue, $skuArr);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $valueArr = array();
        foreach ($this->variantData as $variant) {
            $valueArr[] = $variant['value'];
        }
        return join($this->valueGlue, $valueArr);
    }
}
