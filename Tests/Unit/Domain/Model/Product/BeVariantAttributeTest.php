<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class BeVariantAttributeTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    protected $beVariantAttribute = null;

    /**
     *
     */
    public function setUp()
    {
        $this->beVariantAttribute = new \Extcode\Cart\Domain\Model\Product\BeVariantAttribute();
    }

    /**
     * @test
     */
    public function getBeVariantAttributeOptionsInitiallyIsEmpty()
    {
        $this->assertEmpty(
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function setTransactionsSetsTransactions()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorage->attach($beVariantAttributeOption1);
        $objectStorage->attach($beVariantAttributeOption2);

        $this->beVariantAttribute->setBeVariantAttributeOptions($objectStorage);

        $this->assertContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function addTransactionAddsTransaction()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption1);
        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption2);

        $this->assertContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function removeTransactionRemovesTransaction()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption1);
        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption2);
        $this->beVariantAttribute->removeBeVariantAttributeOption($beVariantAttributeOption1);

        $this->assertNotContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }
}
