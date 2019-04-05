<?php
declare(strict_types=1);
namespace Extcode\Cart\Hooks;

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
