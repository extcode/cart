<?php

namespace Extcode\Cart\Tests\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TagTest extends UnitTestCase
{
    /**
     * Product Tag
     *
     * @var \Extcode\Cart\Domain\Model\Tag
     */
    protected $tag = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     *
     */
    public function setUp()
    {
        $this->title = 'Title';

        $this->tag = new \Extcode\Cart\Domain\Model\Tag(
            $this->title
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->tag = new \Extcode\Cart\Domain\Model\Tag(
            null
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->tag->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $newTitle = 'new Title';

        $this->tag->setTitle($newTitle);

        $this->assertSame(
            $newTitle,
            $this->tag->getTitle()
        );
    }
}
