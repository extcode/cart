<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ServiceTest extends UnitTestCase
{
    /**
     * @var int
     */
    protected $id = 1;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Service
     */
    protected $service;

    /**
     *
     */
    public function setUp()
    {
        $this->config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open'
        ];

        $this->service = new \Extcode\Cart\Domain\Model\Cart\Service(
            $this->id,
            $this->config
        );
    }

    /**
     * @test
     */
    public function getServiceIdReturnsServiceIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->service->getId()
        );
    }

    /**
     * @test
     */
    public function getServiceConfigReturnsServiceConfigSetByConstructor()
    {
        $this->assertSame(
            $this->config,
            $this->service->getConfig()
        );
    }
}
