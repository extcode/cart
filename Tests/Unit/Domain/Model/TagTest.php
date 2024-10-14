<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Tag;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Tag::class)]
class TagTest extends UnitTestCase
{
    protected Tag $tag;

    protected string $title = '';

    public function setUp(): void
    {
        $this->title = 'Title';

        $this->tag = new Tag(
            $this->title
        );

        parent::setUp();
    }

    #[Test]
    public function constructCouponWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->tag = new Tag(
            null
        );
    }

    #[Test]
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->tag->getTitle()
        );
    }

    #[Test]
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
