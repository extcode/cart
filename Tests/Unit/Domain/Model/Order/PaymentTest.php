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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Payment::class)]
class PaymentTest extends UnitTestCase
{
    protected Payment $payment;

    public function setUp(): void
    {
        $this->payment = new Payment();

        parent::setUp();
    }

    #[Test]
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

    #[Test]
    public function getProviderInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->payment->getProvider()
        );
    }

    #[Test]
    public function setProviderSetsProvider(): void
    {
        $provider = 'provider';
        $this->payment->setProvider($provider);

        self::assertSame(
            $provider,
            $this->payment->getProvider()
        );
    }

    #[Test]
    public function getTransactionsInitiallyIsEmpty(): void
    {
        self::assertEmpty(
            $this->payment->getTransactions()
        );
    }

    #[Test]
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

    #[Test]
    public function addTransactionAddsTransaction(): void
    {
        $transaction = new Transaction();

        $this->payment->addTransaction($transaction);

        self::assertContains(
            $transaction,
            $this->payment->getTransactions()
        );
    }

    #[Test]
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
