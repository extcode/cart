<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Tag;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

trait TagTrait
{
    /**
     * @var ObjectStorage<Tag>
     */
    protected ObjectStorage $tags;

    public function __construct()
    {
        $this->tags = new ObjectStorage();
    }

    public function getTags(): ObjectStorage
    {
        return $this->tags;
    }

    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags->attach($tag);
    }

    public function removeTag(Tag $tag): void
    {
        $this->tags->detach($tag);
    }
}
