<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PaymentTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment;

    public function setUp(): void
    {
        $this->payment = new \Extcode\Cart\Domain\Model\Order\Payment();
    }

    /**
     * @test
     */
    public function toArrayReturnsArray()
    {
        $provider = 'test_provider';

        $this->payment->setProvider($provider);

        $result = $this->payment->toArray();

        $this->assertArrayHasKey(
            'provider',
            $result
        );
        $this->assertEquals(
            $provider,
            $result['provider']
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
