<?php

namespace Extcode\Cart\Test\Domain\Repository\Product;

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
use Extcode\Cart\Domain\Model\Dto\Product\ProductDemand;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Product Product Repository
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductRepositoryTest extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /** @var
     * \Extcode\Cart\Domain\Repository\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/cart',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->objectManager = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );
        $this->productRepository = $this->objectManager->get(
            \Extcode\Cart\Domain\Repository\Product\ProductRepository::class
        );

        $fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/cart/Tests/Functional/Fixtures/';
        $this->importDataSet($fixturePath . 'pages.xml');
        $this->importDataSet($fixturePath . 'tx_cart_domain_model_product_product.xml');
    }

    /**
     * @test
     */
    public function findDemandedWithGivenSkuReturnsProducts()
    {
        $_GET['id'] = 101;

        $productDemand = $this->objectManager->get(ProductDemand::class);
        $productDemand->setSku('first');

        $products = $this->productRepository->findDemanded($productDemand);

        $this->assertCount(
            1,
            $products
        );
    }

    /**
     * @test
     */
    public function findDemandedWithGivenTitleReturnsProducts()
    {
        $_GET['id'] = 101;

        $productDemand = $this->objectManager->get(ProductDemand::class);
        $productDemand->setTitle('First');

        $products = $this->productRepository->findDemanded($productDemand);

        $this->assertCount(
            1,
            $products
        );

        $productDemand->setTitle('Product');

        $products = $this->productRepository->findDemanded($productDemand);

        $this->assertCount(
            3,
            $products
        );
    }
}
