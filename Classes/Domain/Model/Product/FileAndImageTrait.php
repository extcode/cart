<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

trait FileAndImageTrait
{
    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $files;

    /**
     * @var ObjectStorage<FileReference>
     */
    protected ObjectStorage $images;

    public function __construct()
    {
        $this->files = new ObjectStorage();
        $this->images = new ObjectStorage();
    }

    public function getFiles(): ObjectStorage
    {
        return $this->files;
    }

    public function setFiles(ObjectStorage $files): void
    {
        $this->files = $files;
    }

    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    public function getFirstImage(): ?FileReference
    {
        $images = $this->getImages()->toArray();
        return array_shift($images);
    }

    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }
}
