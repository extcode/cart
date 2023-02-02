<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\CategoryTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CategoryTraitTest extends UnitTestCase
{
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getObjectForTrait(CategoryTrait::class);
    }

    /**
     * @test
     */
    public function getCategoryInitialReturnsNull(): void
    {
        self::assertNull(
            $this->trait->getCategory()
        );
    }

    /**
     * @test
     */
    public function setCategorySetsCategory(): void
    {
        $category = GeneralUtility::makeInstance(Category::class);
        $this->trait->setCategory($category);

        self::assertSame(
            $category,
            $this->trait->getCategory()
        );
    }

    /**
     * @test
     */
    public function getCategoriesInitialReturnsEmptyObjectStorage(): void
    {
        self::assertSame(
            0,
            $this->trait->getCategories()->count()
        );
    }
}
