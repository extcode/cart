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
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment;

    /**
     *
     */
    public function setUp()
    {
        $this->payment = new \Extcode\Cart\Domain\Model\Order\Payment();
    }

    /**
     * @test
     */
    public function toArrayReturnsArray()
    {
        $provider = 'provider';

        $this->payment->setProvider($provider);

        $this->assertArraySubset(
            ['provider' => $provider],
            $this->payment->toArray()
        );
    }

    /**
     * @test
     */
    public function getProviderInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->payment->getProvider()
        );
    }

    /**
     * @test
     */
    public function setProviderSetsProvider()
    {
        $provider = 'provider';
        $this->payment->setProvider($provider);

        $this->assertSame(
            $provider,
            $this->payment->getProvider()
        );
    }

    /**
     * @test
     */
    public function getTransactionsInitiallyIsEmpty()
    {
        $this->assertEmpty(
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function setTransactionsSetsTransactions()
    {
        $transaction1 = new \Extcode\Cart\Domain\Model\Order\Transaction();
        $transaction2 = new \Extcode\Cart\Domain\Model\Order\Transaction();

        $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorage->attach($transaction1);
        $objectStorage->attach($transaction2);

        $this->payment->setTransactions($objectStorage);

        $this->assertContains(
            $transaction1,
            $this->payment->getTransactions()
        );
        $this->assertContains(
            $transaction2,
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function addTransactionAddsTransaction()
    {
        $transaction = new \Extcode\Cart\Domain\Model\Order\Transaction();

        $this->payment->addTransaction($transaction);

        $this->assertContains(
            $transaction,
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function removeTransactionRemovesTransaction()
    {
        $transaction = new \Extcode\Cart\Domain\Model\Order\Transaction();

        $this->payment->addTransaction($transaction);
        $this->payment->removeTransaction($transaction);

        $this->assertEmpty(
            $this->payment->getTransactions()
        );
    }
}
