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
use TYPO3\CMS\Core\Mail\FluidEmail;

interface MailAttachmentHookInterface
{
    /**
     * @param FluidEmail $mailMessage
     * @param Item $item
     * @param string $type = ['buyer' | 'seller']
     *
     * @return FluidEmail
     */
    public function getMailAttachments(FluidEmail $mailMessage, Item $item, string $type): FluidEmail;
}
