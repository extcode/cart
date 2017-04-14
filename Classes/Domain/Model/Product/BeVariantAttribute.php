<?php

namespace Extcode\Cart\Domain\Model\Product;

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

/**
 * Product BeVariantAttribute Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariantAttribute extends \Extcode\Cart\Domain\Model\Product\AbstractProduct
{
    /**
     * BeVariantAttributeOptions
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption>
     */
    protected $beVariantAttributeOptions = null;

    /**
     * __construct
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->beVariantAttributeOptions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Adds BeVariantAttributeOption
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption
     */
    public function addBeVariantAttributeOption(
        \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption
    ) {
        $this->beVariantAttributeOptions->attach($beVariantAttributeOption);
    }

    /**
     * Removes BeVariantAttributeOption
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption
     */
    public function removeBeVariantAttributeOption(
        \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption
    ) {
        $this->beVariantAttributeOptions->detach($beVariantAttributeOption);
    }

    /**
     * Returns BeVariantAttributeOptions
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption>
     */
    public function getBeVariantAttributeOptions()
    {
        return $this->beVariantAttributeOptions;
    }

    /**
     * Sets BeVariantAttributeOptions
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $beVariantAttributeOptions
     */
    public function setBeVariantAttributeOptions(
        \TYPO3\CMS\Extbase\Persistence\ObjectStorage $beVariantAttributeOptions
    ) {
        $this->beVariantAttributeOptions = $beVariantAttributeOptions;
    }
}
