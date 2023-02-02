<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\TagTrait;
use Extcode\Cart\Domain\Model\Tag;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TagTraitTest extends UnitTestCase
{
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getObjectForTrait(TagTrait::class);
    }

    /**
     * @test
     */
    public function getTagsInitialReturnsEmptyObjectStorage(): void
    {
        self::assertSame(
            0,
            $this->trait->getTags()->count()
        );
    }

    /**
     * @test
     */
    public function setTagsSetsTagsObjectStorage(): void
    {
        $tag = GeneralUtility::makeInstance(
            Tag::class,
            'Test Tag'
        );

        $tags = GeneralUtility::makeInstance(ObjectStorage::class);
        $tags->attach($tag);

        $this->trait->setTags($tags);

        self::assertSame(
            1,
            $this->trait->getTags()->count()
        );

        self::assertSame(
            $tags,
            $this->trait->getTags()
        );
    }

    /**
     * @test
     */
    public function addTagAddsTagToObjectStorage(): void
    {
        $tag = GeneralUtility::makeInstance(
            Tag::class,
            'Test Tag'
        );

        $tags = $this->trait->getTags();
        $tags->attach($tag);

        $this->trait->addTag($tag);

        self::assertSame(
            1,
            $this->trait->getTags()->count()
        );

        self::assertSame(
            $tags,
            $this->trait->getTags()
        );
    }

    /**
     * @test
     */
    public function removeTagRemovesTagToObjectStorage(): void
    {
        $tag1 = GeneralUtility::makeInstance(
            Tag::class,
            'Test Tag 1'
        );
        $tag2 = GeneralUtility::makeInstance(
            Tag::class,
            'Test Tag 2'
        );

        $tags = GeneralUtility::makeInstance(ObjectStorage::class);
        $tags->attach($tag1);
        $tags->attach($tag2);
        $this->trait->setTags($tags);

        $tags->detach($tag1);
        $this->trait->removeTag($tag1);

        self::assertSame(
            1,
            $this->trait->getTags()->count()
        );

        self::assertSame(
            $tags,
            $this->trait->getTags()
        );
    }
}
