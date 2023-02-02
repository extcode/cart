<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AbstractService;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AbstractServiceTest extends UnitTestCase
{
    protected AbstractService $service;

    public function setUp(): void
    {
        $this->service = $this->getMockForAbstractClass(AbstractService::class);

        parent::setUp();
    }

    /**
     * @test
     */
    public function toArrayReturnsArray(): void
    {
        $serviceCountry = 'de';
        $serviceId = 1;
        $name = 'name';
        $note = 'note';
        $status = 'status';
        $gross = 10.00;
        $net = 8.40;
        $taxClass = new TaxClass();
        $taxClass->setTitle('normal');
        $taxClass->setValue('19');
        $taxClass->setCalc(0.19);
        $tax = 1.60;

        $this->service->setServiceCountry($serviceCountry);
        $this->service->setServiceId($serviceId);
        $this->service->setName($name);
        $this->service->setStatus($status);
        $this->service->setNote($note);
        $this->service->setGross($gross);
        $this->service->setNet($net);
        $this->service->setTax($tax);

        $serviceArr = [
            'service_country' => $serviceCountry,
            'service_id' => $serviceId,
            'name' => $name,
            'status' => $status,
            'net' => $net,
            'gross' => $gross,
            'tax' => $tax,
            'taxClass' => null,
            'note' => $note,
        ];

        self::assertEquals(
            $serviceArr,
            $this->service->toArray()
        );

        //with taxClass
        $this->service->setTaxClass($taxClass);

        $serviceArr['taxClass'] = $taxClass->toArray();

        self::assertEquals(
            $serviceArr,
            $this->service->toArray()
        );
    }

    /**
     * @test
     */
    public function getItemInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->service->getItem()
        );
    }

    /**
     * @test
     */
    public function setItemSetsItem(): void
    {
        $item = new Item();

        $this->service->setItem($item);

        self::assertSame(
            $item,
            $this->service->getItem()
        );
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->service->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName(): void
    {
        $this->service->setName('foo bar');

        self::assertSame(
            'foo bar',
            $this->service->getName()
        );
    }

    /**
     * @test
     */
    public function getStatusInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            'open',
            $this->service->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusSetsStatus(): void
    {
        $this->service->setStatus('paid');

        self::assertSame(
            'paid',
            $this->service->getStatus()
        );
    }

    /**
     * @test
     */
    public function getNoteInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->service->getNote()
        );
    }

    /**
     * @test
     */
    public function setNoteSetsNote(): void
    {
        $note = 'note';
        $this->service->setNote($note);

        self::assertSame(
            $note,
            $this->service->getNote()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->service->getGross()
        );
    }

    /**
     * @test
     */
    public function setGrossSetsGross(): void
    {
        $this->service->setGross(1234.56);

        self::assertSame(
            1234.56,
            $this->service->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->service->getNet()
        );
    }

    /**
     * @test
     */
    public function setNetSetsNet(): void
    {
        $this->service->setNet(1234.56);

        self::assertSame(
            1234.56,
            $this->service->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->service->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function setTaxClassSetsTaxClass(): void
    {
        $taxClass = new TaxClass();
        $taxClass->setTitle('normal');
        $taxClass->setValue('19');
        $taxClass->setCalc(0.19);

        $this->service->setTaxClass($taxClass);

        self::assertSame(
            $taxClass,
            $this->service->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->service->getTax()
        );
    }

    /**
     * @test
     */
    public function setTaxSetsTax(): void
    {
        $this->service->setTax(1234.56);

        self::assertSame(
            1234.56,
            $this->service->getTax()
        );
    }
}
