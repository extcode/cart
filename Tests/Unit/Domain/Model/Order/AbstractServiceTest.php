<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lorenz <ext.cart@extco.de>, extco.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class AbstractServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $service = null;

    public function setUp()
    {
        $this->service = $this->getMockForAbstractClass('\Extcode\Cart\Domain\Model\Order\AbstractService');
    }

    /**
     * @test
     */
    public function toArrayReturnsArray()
    {
        $serviceCountry = 'de';
        $serviceId = 1;
        $name = 'name';
        $note = 'note';
        $status = 'status';
        $gross = 10.00;
        $net = 8.40;
        $taxClass = new \Extcode\Cart\Domain\Model\Order\TaxClass('normal', '19', 0.19);
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
            'note' => $note
        ];

        $this->assertEquals(
            $serviceArr,
            $this->service->toArray()
        );

        //with taxClass
        $this->service->setTaxClass($taxClass);

        $serviceArr['taxClass'] = $taxClass->toArray();

        $this->assertEquals(
            $serviceArr,
            $this->service->toArray()
        );
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->service->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName()
    {
        $this->service->setName('foo bar');

        $this->assertSame(
            'foo bar',
            $this->service->getName()
        );
    }

    /**
     * @test
     */
    public function getStatusInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            'open',
            $this->service->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusSetsStatus()
    {
        $this->service->setStatus('paid');

        $this->assertSame(
            'paid',
            $this->service->getStatus()
        );
    }

    /**
     * @test
     */
    public function getNoteInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->service->getNote()
        );
    }

    /**
     * @test
     */
    public function setNoteSetsNote()
    {
        $note = 'note';
        $this->service->setNote($note);

        $this->assertSame(
            $note,
            $this->service->getNote()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->service->getGross()
        );
    }

    /**
     * @test
     */
    public function setGrossSetsGross()
    {
        $this->service->setGross(1234.56);

        $this->assertSame(
            1234.56,
            $this->service->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->service->getNet()
        );
    }

    /**
     * @test
     */
    public function setNetSetsNet()
    {
        $this->service->setNet(1234.56);

        $this->assertSame(
            1234.56,
            $this->service->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsNull()
    {
        $this->assertNull(
            $this->service->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function setTaxClassSetsTaxClass()
    {
        $taxClass = new \Extcode\Cart\Domain\Model\Order\TaxClass('normal', '19', 0.19);

        $this->service->setTaxClass($taxClass);

        $this->assertSame(
            $taxClass,
            $this->service->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->service->getTax()
        );
    }

    /**
     * @test
     */
    public function setTaxSetsTax()
    {
        $this->service->setTax(1234.56);

        $this->assertSame(
            1234.56,
            $this->service->getTax()
        );
    }
}
