<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * FeVariant Test
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class FeVariantTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Product Frontend Variant
     *
     * @var \Extcode\Cart\Domain\Model\Product\FeVariant
     */
    protected $feVariant = null;

    public function setUp()
    {
        $this->feVariant = new \Extcode\Cart\Domain\Model\Product\FeVariant;
    }

    /**
     * @test
     */
    public function getSkuInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->feVariant->getSku()
        );
    }

    /**
     * @test
     */
    public function setSkuForStringSetsSku()
    {
        $this->feVariant->setSku('SKU');

        $this->assertSame(
            'SKU',
            $this->feVariant->getSku()
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->feVariant->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->feVariant->setTitle('Title');

        $this->assertSame(
            'Title',
            $this->feVariant->getTitle()
        );
    }

    /**
     * @test
     */
    public function getDescriptionInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->feVariant->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->feVariant->setDescription('Description');

        $this->assertSame(
            'Description',
            $this->feVariant->getDescription()
        );
    }

    /**
     * @test
     */
    public function getIsRequiredInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->feVariant->getIsRequired()
        );
    }

    /**
     * @test
     */
    public function setIsRequiredSetsIsRequired()
    {
        $this->feVariant->setIsRequired(true);

        $this->assertTrue(
            $this->feVariant->getIsRequired()
        );
    }
}
