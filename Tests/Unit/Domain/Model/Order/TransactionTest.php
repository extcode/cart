<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Transaction;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TransactionTest extends UnitTestCase
{
    /**
     * @var Transaction
     */
    protected $transaction;

    public function setUp(): void
    {
        $this->transaction = new Transaction();

        parent::setUp();
    }

    /**
     * @test
     */
    public function getTxnIdInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->transaction->getTxnId()
        );
    }

    /**
     * @test
     */
    public function setTxnIdSetsTnxId(): void
    {
        $transactionId = 'transaction-id';

        $this->transaction->setTxnId($transactionId);

        self::assertSame(
            $transactionId,
            $this->transaction->getTxnId()
        );
    }
}
