<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\MailEnvelope;
use Extcode\Cart\Mail\MailException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(MailEnvelope::class)]
final class MailEnvelopeTest extends UnitTestCase
{
    #[Test]
    public function anEnvelopeWithValidAddressesIsCreated(): void
    {
        $envelope = new MailEnvelope('shop@example.com', 'Shop', ['customer@example.com'], ['cc@example.com'], ['bcc@example.com'], ['reply@example.com']);

        self::assertSame(['customer@example.com'], $envelope->to);
    }

    #[Test]
    public function anEnvelopeWithoutSenderIsCreated(): void
    {
        $envelope = new MailEnvelope('', '', ['customer@example.com']);

        self::assertSame('', $envelope->fromAddress, 'The default sender of TYPO3 is used then.');
    }

    /**
     * @param list<string> $to
     * @param list<string> $cc
     * @param list<string> $bcc
     * @param list<string> $replyTo
     */
    #[DataProvider('invalidAddressDataProvider')]
    #[Test]
    public function anEnvelopeWithAnInvalidAddressThrowsException(string $fromAddress, array $to, array $cc, array $bcc, array $replyTo): void
    {
        $this->expectExceptionObject(new MailException('The email address "invalid" is invalid.', 1795130002));

        new MailEnvelope($fromAddress, '', $to, $cc, $bcc, $replyTo);
    }

    /**
     * @return iterable<string, array{fromAddress: string, to: list<string>, cc: list<string>, bcc: list<string>, replyTo: list<string>}>
     */
    public static function invalidAddressDataProvider(): iterable
    {
        $valid = ['valid@example.com'];

        yield 'from' => ['fromAddress' => 'invalid', 'to' => $valid, 'cc' => [], 'bcc' => [], 'replyTo' => []];
        yield 'to' => ['fromAddress' => '', 'to' => ['valid@example.com', 'invalid'], 'cc' => [], 'bcc' => [], 'replyTo' => []];
        yield 'cc' => ['fromAddress' => '', 'to' => $valid, 'cc' => ['invalid'], 'bcc' => [], 'replyTo' => []];
        yield 'bcc' => ['fromAddress' => '', 'to' => $valid, 'cc' => [], 'bcc' => ['invalid'], 'replyTo' => []];
        yield 'reply-to' => ['fromAddress' => '', 'to' => $valid, 'cc' => [], 'bcc' => [], 'replyTo' => ['invalid']];
    }
}
