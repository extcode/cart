<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class TagTest extends UnitTestCase
{
    /**
     * Product Tag
     *
     * @var \Extcode\Cart\Domain\Model\Product\Tag
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

        $this->tag = new \Extcode\Cart\Domain\Model\Product\Tag(
            $this->title
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1460206410
        );

        $this->tag = new \Extcode\Cart\Domain\Model\Product\Tag(
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
