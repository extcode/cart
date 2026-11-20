<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Event\Mail\AttachmentEvent;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Collects the attachments of an email to the buyer or to the seller with the
 * AttachmentEvent, for the emails at the end of an order and on status changes.
 */
final readonly class AttachmentCollector
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private LogServiceInterface $logService,
    ) {
    }

    /**
     * @param string $recipient "buyer" or "seller", the type of the AttachmentEvent
     *
     * @throws MailException if a listener of the AttachmentEvent fails, the email is not sent without its attachments
     *
     * @return list<string> the existing files of the AttachmentEvent, missing files are written to the order log
     */
    public function collect(string $recipient, Item $orderItem): array
    {
        $attachmentEvent = new AttachmentEvent($recipient, $orderItem);
        try {
            $this->eventDispatcher->dispatch($attachmentEvent);
        } catch (Throwable $exception) {
            throw new MailException(
                'The attachments could not be collected: ' . $exception->getMessage(),
                1795140003,
                $exception
            );
        }

        $attachments = [];
        foreach ($attachmentEvent->getAttachments() as $attachment) {
            if (is_file($attachment)) {
                $attachments[] = $attachment;
                continue;
            }
            $this->logService->write(
                Log::warning(
                    self::getOrderItemUid($orderItem),
                    'addAttachments',
                    'Mail could add attachment ' . $attachment . ' to mail.',
                    [
                        'time' => time(),
                    ]
                )
            );
        }

        return $attachments;
    }

    private static function getOrderItemUid(Item $orderItem): int
    {
        $orderItemUid = $orderItem->getUid();

        if (is_null($orderItemUid)) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }

        return $orderItemUid;
    }
}
