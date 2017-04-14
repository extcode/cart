<?php

namespace Extcode\Cart\Domain\Model;

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
 * Category Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category
{
    /**
     * Images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $images;

    /**
     * Cart Product List Pid
     *
     * @var int
     */
    protected $cartProductListPid;

    /**
     * Cart Product Single Pid
     *
     * @var int
     */
    protected $cartProductSinglePid;

    /**
     * Returns Cart Product List Pid
     *
     * @return int
     */
    public function getCartProductListPid()
    {
        return $this->cartProductListPid;
    }

    /**
     * Returns Cart Product Single Pid
     *
     * @return int
     */
    public function getCartProductSinglePid()
    {
        return $this->cartProductSinglePid;
    }

    /**
     * Returns images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Returns the first image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference|null
     */
    public function getFirstImage()
    {
        $images = $this->getImages();
        foreach ($images as $image) {
            return $image;
        }
        return null;
    }
}
