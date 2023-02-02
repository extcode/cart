<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Model\Order\Transaction;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PaymentTest extends UnitTestCase
{
    /**
     * @var Payment
     */
    protected $payment;

    public function setUp(): void
    {
        $this->payment = new Payment();

        parent::setUp();
    }

    /**
     * @test
     */
    public function toArrayReturnsArray(): void
    {
        $provider = 'test_provider';

        $this->payment->setProvider($provider);

        $result = $this->payment->toArray();

        self::assertArrayHasKey(
            'provider',
            $result
        );
        self::assertEquals(
            $provider,
            $result['provider']
        );
    }

    /**
     * @test
     */
    public function getProviderInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->payment->getProvider()
        );
    }

    /**
     * @test
     */
    public function setProviderSetsProvider(): void
    {
        $provider = 'provider';
        $this->payment->setProvider($provider);

        self::assertSame(
            $provider,
            $this->payment->getProvider()
        );
    }

    /**
     * @test
     */
    public function getTransactionsInitiallyIsEmpty(): void
    {
        self::assertEmpty(
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function setTransactionsSetsTransactions(): void
    {
        $transaction1 = new Transaction();
        $transaction2 = new Transaction();

        $objectStorage = new ObjectStorage();
        $objectStorage->attach($transaction1);
        $objectStorage->attach($transaction2);

        $this->payment->setTransactions($objectStorage);

        self::assertContains(
            $transaction1,
            $this->payment->getTransactions()
        );
        self::assertContains(
            $transaction2,
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function addTransactionAddsTransaction(): void
    {
        $transaction = new Transaction();

        $this->payment->addTransaction($transaction);

        self::assertContains(
            $transaction,
            $this->payment->getTransactions()
        );
    }

    /**
     * @test
     */
    public function removeTransactionRemovesTransaction(): void
    {
        $transaction = new Transaction();

        $this->payment->addTransaction($transaction);
        $this->payment->removeTransaction($transaction);

        self::assertEmpty(
            $this->payment->getTransactions()
        );
    }
}
