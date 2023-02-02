<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Transaction extends AbstractEntity
{
    protected ?Payment $payment = null;

    protected $txnId = '';

    protected string $txnTxt = '';

    protected string $status = '';

    protected string $externalStatusCode = '';

    protected string $note = '';

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function getTxnId(): string
    {
        return $this->txnId;
    }

    public function setTxnId(string $txnId): void
    {
        $this->txnId = $txnId;
    }

    public function getTxnTxt(): string
    {
        return $this->txnTxt;
    }

    public function setTxnTxt(string $txnTxt): void
    {
        $this->txnTxt = $txnTxt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getExternalStatusCode(): string
    {
        return $this->externalStatusCode;
    }

    public function setExternalStatusCode(string $externalStatusCode): void
    {
        $this->externalStatusCode = $externalStatusCode;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }
}
