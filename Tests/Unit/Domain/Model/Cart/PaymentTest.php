<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

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

/**
 * Payment Test
 *
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class PaymentTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Id
     *
     * @var int
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Status
     *
     * @var int
     */
    protected $status;

    /**
     * Note
     *
     * @var string
     */
    protected $note;

    /**
     * Is Net Price
     *
     * @var bool
     */
    protected $isNetPrice;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     */
    protected $taxClass;

    /**
     * Payment
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Payment $payment
     */
    protected $payment;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->id = 1;
        $this->name = 'Service';
        $this->status = 0;
        $this->note = 'note';
        $this->isNetPrice = 0;

        $this->payment = new \Extcode\Cart\Domain\Model\Cart\Payment(
            $this->id,
            $this->name,
            $this->taxClass,
            $this->status,
            $this->note,
            $this->isNetPrice
        );
    }

    /**
     * @test
     */
    public function getCartProductIdReturnsProductIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->payment->getId()
        );
    }
}
