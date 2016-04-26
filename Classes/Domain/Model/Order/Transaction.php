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
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Transaction extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * txnId
     *
     * @var string
     * @validate NotEmpty
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
     * TxnTxt
     *
     * @var string
     */
    protected $note = '';

    /**
     * @return string
     */
    public function getTxnId()
    {
        return $this->txnId;
    }

    /**
     * @param string $txnId
     * @return void
     */
    public function setTxnId($txnId)
    {
        $this->txnId = $txnId;
    }

    /**
     * Sets TxnTxt
     *
     * @param string $txnTxt
     *
     * @return void
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
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Sets Note
     *
     * @param string $note
     *
     * @return void
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
