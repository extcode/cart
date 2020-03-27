<?php
declare(strict_types=1);

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Core\Mail\MailMessage;

interface MailAttachmentHookInterface
{
    /**
     * @param MailMessage $mailMessage
     * @param Item $item
     * @param string $type = ['buyer' | 'seller']
     *
     * @return MailMessage
     */
    public function getMailAttachments(MailMessage $mailMessage, Item $item, string $type): MailMessage;
}
