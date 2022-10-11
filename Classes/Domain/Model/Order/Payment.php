<?php

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
    /**
     * @var string
     */
    protected $provider = '';

    /**
     * @var ObjectStorage<Transaction>
     * @Lazy
     */
    protected $transactions = null;

    public function __construct()
    {
        $this->transactions = new ObjectStorage();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $payment = parent::toArray();

        $payment['provider'] = $this->getProvider();

        return $payment;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider(string $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction)
    {
        $this->transactions->attach($transaction);
    }

    /**
     * @param Transaction $transaction
     */
    public function removeTransaction(Transaction $transaction)
    {
        $this->transactions->detach($transaction);
    }

    /**
     * @return ObjectStorage
     */
    public function getTransactions(): ObjectStorage
    {
        return $this->transactions;
    }

    /**
     * @param ObjectStorage<Transaction> $transactions
     */
    public function setTransactions(ObjectStorage $transactions)
    {
        $this->transactions = $transactions;
    }
}
