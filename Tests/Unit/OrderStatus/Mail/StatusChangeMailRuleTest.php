<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\OrderStatus\Mail\StatusChangeMailRule;
use Extcode\Cart\OrderStatus\ServiceType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(StatusChangeMailRule::class)]
final class StatusChangeMailRuleTest extends UnitTestCase
{
    #[Test]
    public function fromArrayCreatesTheRule(): void
    {
        $rule = StatusChangeMailRule::fromArray('pendingToPaid', ServiceType::Payment, [
            'from' => 'pending',
            'to' => ' paid ',
            'recipients' => ['seller', 'buyer'],
        ]);

        self::assertSame('pendingToPaid', $rule->key);
        self::assertSame(ServiceType::Payment, $rule->serviceType);
        self::assertSame(['pending'], $rule->fromStatus);
        self::assertSame('paid', $rule->toStatus);
        self::assertSame('Mail/StatusChange/Payment/PendingToPaid', $rule->template);
        self::assertSame(['buyer', 'seller'], $rule->recipients, 'The buyer is first.');
        self::assertSame('Mail/StatusChange/Payment/PendingToPaid/Buyer', $rule->getTemplate('buyer'));
        self::assertSame('Mail/StatusChange/Payment/PendingToPaid/Seller', $rule->getTemplate('seller'));
    }

    #[Test]
    public function fromArrayTakesTheGivenTemplateFolder(): void
    {
        $rule = StatusChangeMailRule::fromArray('10', ServiceType::Shipping, [
            'from' => '*',
            'to' => 'shipped',
            'template' => 'Mail/StatusChange/Shipped',
            'recipients' => ['buyer'],
        ]);

        self::assertNull($rule->fromStatus, '"*" is any status.');
        self::assertSame('Mail/StatusChange/Shipped/Buyer', $rule->getTemplate('buyer'));
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('lists')]
    #[Test]
    public function fromArrayAcceptsAListOfOldStatusAndRecipients(mixed $list, array $expected): void
    {
        $rule = StatusChangeMailRule::fromArray('toPaid', ServiceType::Payment, [
            'from' => $list,
            'to' => 'paid',
            'recipients' => 'buyer, seller',
        ]);

        self::assertSame($expected, $rule->fromStatus);
        self::assertSame(['buyer', 'seller'], $rule->recipients);
    }

    /**
     * @return iterable<string, array{mixed, list<string>}>
     */
    public static function lists(): iterable
    {
        yield 'list' => [['open', ' pending '], ['open', 'pending']];
        yield 'comma separated string' => ['open, pending', ['open', 'pending']];
    }

    /**
     * @param array<string, mixed> $configuration
     */
    #[DataProvider('invalidConfigurations')]
    #[Test]
    public function fromArrayThrowsAnExceptionForAnInvalidConfiguration(string $key, array $configuration, InvalidMailConfigurationException $exception): void
    {
        $this->expectExceptionObject($exception);

        StatusChangeMailRule::fromArray($key, ServiceType::Payment, $configuration);
    }

    /**
     * @return iterable<string, array{string, array<string, mixed>, InvalidMailConfigurationException}>
     */
    public static function invalidConfigurations(): iterable
    {
        yield 'without from' => [
            'toPaid',
            ['to' => 'paid', 'recipients' => ['buyer']],
            new InvalidMailConfigurationException('The old status "from" is missing, use "*" for any status.', 1795120001),
        ];
        yield 'without to' => [
            'fromOpen',
            ['from' => 'open', 'recipients' => ['buyer']],
            new InvalidMailConfigurationException('The new status "to" is missing.', 1795120002),
        ];
        yield 'template no string' => [
            'openToPaid',
            ['from' => 'open', 'to' => 'paid', 'template' => [], 'recipients' => ['buyer']],
            new InvalidMailConfigurationException('The template must be a string.', 1795120003),
        ];
        yield 'key no template folder' => [
            'open-to-paid',
            ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
            new InvalidMailConfigurationException('The key is no valid template folder, it must match /^[a-zA-Z][a-zA-Z0-9]*$/, or set "template".', 1795120004),
        ];
        yield 'without recipients' => [
            'openToPaid',
            ['from' => 'open', 'to' => 'paid'],
            new InvalidMailConfigurationException('The "recipients" are missing, e.g. "recipients: [buyer]".', 1795120005),
        ];
        yield 'unknown recipient' => [
            'openToPaid',
            ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer', 'carrier']],
            new InvalidMailConfigurationException('The recipients "carrier" are unknown, allowed are "buyer", "seller".', 1795120006),
        ];
    }

    #[Test]
    public function matchesTheTransition(): void
    {
        $rule = new StatusChangeMailRule('openToPaid', ServiceType::Payment, ['open', 'pending'], 'paid', 'Mail/StatusChange/Payment/OpenToPaid', ['buyer']);
        $anyStatus = new StatusChangeMailRule('anyToPaid', ServiceType::Payment, null, 'paid', 'Mail/StatusChange/Payment/AnyToPaid', ['buyer']);

        self::assertTrue($rule->matches('pending', 'paid'));
        self::assertFalse($rule->matches('canceled', 'paid'));
        self::assertFalse($rule->matches('open', 'canceled'));
        self::assertTrue($anyStatus->matches('canceled', 'paid'));
    }
}
