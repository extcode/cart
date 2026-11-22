<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Payment extends AbstractService
{
    protected string $provider = '';

    /**
     * @var ObjectStorage<Transaction>
     */
    #[Lazy]
    protected ?ObjectStorage $transactions = null;

    public function __construct()
    {
        $this->transactions = new ObjectStorage();
    }

    public function toArray(): array
    {
        $payment = parent::toArray();

        $payment['provider'] = $this->getProvider();

        return $payment;
    }

    public function isOpen(): bool
    {
        return $this->getStatus() === 'open';
    }

    public function isPending(): bool
    {
        return $this->getStatus() === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->getStatus() === 'paid';
    }

    public function isCanceled(): bool
    {
        return $this->getStatus() === 'canceled';
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions->attach($transaction);
    }

    public function removeTransaction(Transaction $transaction): void
    {
        $this->transactions->detach($transaction);
    }

    /**
     * @return ObjectStorage<Transaction>
     */
    public function getTransactions(): ObjectStorage
    {
        return $this->transactions;
    }

    /**
     * @param ObjectStorage<Transaction> $transactions
     */
    public function setTransactions(ObjectStorage $transactions): void
    {
        $this->transactions = $transactions;
    }
}
