<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Transaction;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Transaction::class)]
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

    #[Test]
    public function getTxnIdInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->transaction->getTxnId()
        );
    }

    #[Test]
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
