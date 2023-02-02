<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\StockTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class StockTraitTest extends UnitTestCase
{
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getObjectForTrait(StockTrait::class);
    }

    /**
     * @test
     */
    public function isHandleStockReturnsInitialFalse(): void
    {
        self::assertFalse(
            $this->trait->isHandleStock()
        );
    }

    /**
     * @test
     */
    public function setIsHandleStockSetsIsHandleStock(): void
    {
        $this->trait->setIsHandleStock(true);

        self::assertTrue(
            $this->trait->isHandleStock()
        );
    }

    /**
     * @test
     */
    public function getStockReturnsInitialZero(): void
    {
        self::assertSame(
            0,
            $this->trait->getStock()
        );
    }

    /**
     * @test
     */
    public function setStockSetsStock(): void
    {
        $this->trait->setStock(10);

        self::assertSame(
            10,
            $this->trait->getStock()
        );
    }
}
