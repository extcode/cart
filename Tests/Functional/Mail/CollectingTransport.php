<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Stringable;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

/**
 * A mail transport for tests, which collects the emails instead of sending
 * them, set as $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'].
 */
final class CollectingTransport implements Stringable, TransportInterface
{
    /**
     * @var list<Email>
     */
    private static array $emails = [];

    public function __toString(): string
    {
        return 'collecting://';
    }

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if ($message instanceof Email) {
            // renders a FluidEmail like a real transport does
            $message->getHtmlBody();
            self::$emails[] = $message;
        }

        return null;
    }

    /**
     * @return list<Email>
     */
    public static function getEmails(): array
    {
        return self::$emails;
    }

    public static function reset(): void
    {
        self::$emails = [];
    }
}
