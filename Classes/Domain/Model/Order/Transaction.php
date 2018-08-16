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
 * Order Transaction Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Transaction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Payment
     *
     * @var \Extcode\Cart\Domain\Model\Order\Payment
     */
    protected $payment = null;

    /**
     * txnId
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate NotEmpty
     */
    protected $txnId = '';

    /**
     * TxnTxt
     *
     * @var string
     */
    protected $txnTxt = '';

    /**
     * Status
     *
     * @var string
     */
    protected $status = '';

    /**
     * External Status Code
     *
     * @var string
     */
    protected $externalStatusCode = '';

    /**
     * TxnTxt
     *
     * @var string
     */
    protected $note = '';

    /**
     * Returns the payment
     *
     * @return \Extcode\Cart\Domain\Model\Order\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return string
     */
    public function getTxnId()
    {
        return $this->txnId;
    }

    /**
     * @param string $txnId
     */
    public function setTxnId($txnId)
    {
        $this->txnId = $txnId;
    }

    /**
     * Sets TxnTxt
     *
     * @param string $txnTxt
     */
    public function setTxnTxt($txnTxt)
    {
        $this->txnTxt = $txnTxt;
    }

    /**
     * Gets TxnTxt
     *
     * @return string
     */
    public function getTxnTxt()
    {
        return $this->txnTxt;
    }

    /**
     * Returns Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets Status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getExternalStatusCode()
    {
        return $this->externalStatusCode;
    }

    /**
     * @param string $externalStatusCode
     */
    public function setExternalStatusCode($externalStatusCode)
    {
        $this->externalStatusCode = $externalStatusCode;
    }

    /**
     * Sets Note
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Gets Note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}
