<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Payment;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Attribute\IgnoreValidation;

class PaymentController extends AbstractStatusController
{
    /**
     * @param string $status the new status
     * @param string $fromStatus the status shown in the form, the status is only changed if the order still has it
     */
    public function updateAction(
        #[IgnoreValidation]
        Payment $payment,
        string $status = '',
        string $fromStatus = ''
    ): ResponseInterface {
        return $this->changeStatus($payment, $status, $fromStatus, 'payment');
    }
}
