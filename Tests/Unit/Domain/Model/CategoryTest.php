<?php

namespace Extcode\Cart\Tests\Domain\Model;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class CategoryTest extends UnitTestCase
{

    /**
     * @var \Extcode\Cart\Domain\Model\Category
     */
    protected $category = null;

    protected function setUp()
    {
        $this->category = new \Extcode\Cart\Domain\Model\Category();
    }

    protected function tearDown()
    {
        unset($this->category);
    }

    /**
     * @test
     */
    public function getCartProductSinglePidReturnsSinglePid()
    {
        $cartProductSinglePid = 123;

        $category = $this->getAccessibleMock(
            \Extcode\Cart\Domain\Model\Category::class,
            ['dummy'],
            [],
            '',
            false
        );

        $category->_set('cartProductSinglePid', $cartProductSinglePid);

        $this->assertEquals(
            $cartProductSinglePid,
            $category->getCartProductSinglePid()
        );
    }
}
