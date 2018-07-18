<?php

namespace Extcode\Cart\Domain\Model\Order;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Order Payment Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Payment extends \Extcode\Cart\Domain\Model\Order\AbstractService
{

    /**
     * Provider
     *
     * @var string
     */
    protected $provider = '';

    /**
     * Transactions
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\Transaction>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $transactions = null;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->transactions = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns PaymentArray
     *
     * @return array
     */
    public function toArray()
    {
        $payment = parent::toArray();

        $payment['provider'] = $this->getProvider();

        return $payment;
    }

    /**
     * Returns Provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Sets Provider
     *
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Adds a Transaction
     *
     * @param \Extcode\Cart\Domain\Model\Order\Transaction $transaction
     */
    public function addTransaction(\Extcode\Cart\Domain\Model\Order\Transaction $transaction)
    {
        $this->transactions->attach($transaction);
    }

    /**
     * Removes a Transaction
     *
     * @param \Extcode\Cart\Domain\Model\Order\Transaction $transactionsToRemove
     */
    public function removeTransaction(\Extcode\Cart\Domain\Model\Order\Transaction $transactionsToRemove)
    {
        $this->transactions->detach($transactionsToRemove);
    }

    /**
     * Returns transactions
     *
     * @return  \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * Sets Transaction
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\Transaction> $transactions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
    }
}
