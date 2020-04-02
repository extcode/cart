<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Transaction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment = null;

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $txnId = '';

    /**
     * @var string
     */
    protected $txnTxt = '';

    /**
     * @var string
     */
    protected $status = '';

    /**
     * @var string
     */
    protected $externalStatusCode = '';

    /**
     * @var string
     */
    protected $note = '';

    /**
     * @return Payment|null
     */
    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getTxnId(): string
    {
        return $this->txnId;
    }

    /**
     * @param string $txnId
     */
    public function setTxnId(string $txnId)
    {
        $this->txnId = $txnId;
    }

    /**
     * @return string
     */
    public function getTxnTxt(): string
    {
        return $this->txnTxt;
    }

    /**
     * @param string $txnTxt
     */
    public function setTxnTxt(string $txnTxt)
    {
        $this->txnTxt = $txnTxt;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getExternalStatusCode(): string
    {
        return $this->externalStatusCode;
    }

    /**
     * @param string $externalStatusCode
     */
    public function setExternalStatusCode(string $externalStatusCode)
    {
        $this->externalStatusCode = $externalStatusCode;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note)
    {
        $this->note = $note;
    }
}
