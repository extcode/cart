<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lorenz <ext.cart@extco.de>, extco.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class TransactionTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Transaction
     */
    protected $transaction = null;

    /**
     *
     */
    public function setUp()
    {
        $this->transaction = new \Extcode\Cart\Domain\Model\Order\Transaction();
    }

    /**
     * @test
     */
    public function getTxnIdInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->transaction->getTxnId()
        );
    }

    /**
     * @test
     */
    public function setTxnIdSetsTnxId()
    {
        $transactionId = 'transaction-id';

        $this->transaction->setTxnId($transactionId);

        $this->assertSame(
            $transactionId,
            $this->transaction->getTxnId()
        );
    }
}
