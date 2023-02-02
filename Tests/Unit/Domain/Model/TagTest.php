<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Tag;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TagTest extends UnitTestCase
{
    /**
     * @var Tag
     */
    protected $tag;

    /**
     * @var string
     */
    protected $title = '';

    public function setUp(): void
    {
        $this->title = 'Title';

        $this->tag = new Tag(
            $this->title
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->tag = new Tag(
            null
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->tag->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle(): void
    {
        $newTitle = 'new Title';

        $this->tag->setTitle($newTitle);

        self::assertSame(
            $newTitle,
            $this->tag->getTitle()
        );
    }
}
