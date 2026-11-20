<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\SellerMailConfiguration;
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
    public function fromArrayCreatesTheRuleWithAMailConfigurationPerRecipient(): void
    {
        $rule = StatusChangeMailRule::fromArray('pendingToPaid', ServiceType::Payment, [
            'from' => 'pending',
            'to' => ' paid ',
            'recipients' => [
                'seller' => ['toAddress' => 'orders@example.com'],
                'buyer' => ['fromAddress' => 'shop@example.com'],
            ],
        ]);

        self::assertSame('pendingToPaid', $rule->key);
        self::assertSame(ServiceType::Payment, $rule->serviceType);
        self::assertSame(['pending'], $rule->fromStatus);
        self::assertSame('paid', $rule->toStatus);
        self::assertCount(2, $rule->mailConfigurations);
        self::assertInstanceOf(BuyerMailConfiguration::class, $rule->mailConfigurations[0], 'The buyer is first.');
        self::assertSame('Mail/StatusChange/Payment/PendingToPaid/Buyer', $rule->mailConfigurations[0]->template);
        self::assertSame('shop@example.com', $rule->mailConfigurations[0]->fromAddress);
        self::assertInstanceOf(SellerMailConfiguration::class, $rule->mailConfigurations[1]);
        self::assertSame('Mail/StatusChange/Payment/PendingToPaid/Seller', $rule->mailConfigurations[1]->template);
    }

    #[Test]
    public function fromArrayTakesTheGivenTemplateFolder(): void
    {
        $rule = StatusChangeMailRule::fromArray('10', ServiceType::Shipping, [
            'from' => '*',
            'to' => 'shipped',
            'template' => 'Mail/StatusChange/Shipped',
            'recipients' => ['buyer' => []],
        ]);

        self::assertSame('Mail/StatusChange/Shipped/Buyer', $rule->mailConfigurations[0]->template);
        self::assertNull($rule->fromStatus, 'The old status "*" is any status.');
    }

    #[Test]
    public function fromArrayTakesAListOfOldStatus(): void
    {
        $rule = StatusChangeMailRule::fromArray('shipped', ServiceType::Shipping, [
            'from' => 'in_process, open,',
            'to' => 'shipped',
            'recipients' => ['buyer' => []],
        ]);

        self::assertSame(['in_process', 'open'], $rule->fromStatus);
        self::assertSame('Mail/StatusChange/Shipping/Shipped/Buyer', $rule->mailConfigurations[0]->template);
    }

    /**
     * @param null|list<string> $fromStatus
     */
    #[DataProvider('matchesDataProvider')]
    #[Test]
    public function matchesTheTransition(?array $fromStatus, string $oldStatus, string $newStatus, bool $expected): void
    {
        $rule = new StatusChangeMailRule('key', ServiceType::Payment, $fromStatus, 'paid', []);

        self::assertSame($expected, $rule->matches($oldStatus, $newStatus));
    }

    /**
     * @return iterable<string, array{fromStatus: null|list<string>, oldStatus: string, newStatus: string, expected: bool}>
     */
    public static function matchesDataProvider(): iterable
    {
        yield 'old status of the list' => ['fromStatus' => ['open', 'pending'], 'oldStatus' => 'pending', 'newStatus' => 'paid', 'expected' => true];
        yield 'old status not of the list' => ['fromStatus' => ['open'], 'oldStatus' => 'pending', 'newStatus' => 'paid', 'expected' => false];
        yield 'any old status' => ['fromStatus' => null, 'oldStatus' => 'canceled', 'newStatus' => 'paid', 'expected' => true];
        yield 'other new status' => ['fromStatus' => null, 'oldStatus' => 'open', 'newStatus' => 'pending', 'expected' => false];
    }

    /**
     * @param array<array-key, mixed> $configuration
     */
    #[DataProvider('invalidConfigurationDataProvider')]
    #[Test]
    public function fromArrayThrowsExceptionForAnInvalidConfiguration(string $key, array $configuration, InvalidMailConfigurationException $expectedException): void
    {
        $this->expectExceptionObject($expectedException);

        StatusChangeMailRule::fromArray($key, ServiceType::Payment, $configuration);
    }

    /**
     * @return iterable<string, array{key: string, configuration: array<array-key, mixed>, expectedException: InvalidMailConfigurationException}>
     */
    public static function invalidConfigurationDataProvider(): iterable
    {
        $recipients = ['buyer' => []];

        yield 'missing old status' => [
            'key' => 'paid',
            'configuration' => ['to' => 'paid', 'recipients' => $recipients],
            'expectedException' => new InvalidMailConfigurationException('The old status "from" is missing, use "*" for any status.', 1795120001),
        ];
        yield 'missing new status' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => ' ', 'recipients' => $recipients],
            'expectedException' => new InvalidMailConfigurationException('The new status "to" is missing.', 1795120002),
        ];
        yield 'template is no string' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => 'paid', 'template' => ['Mail'], 'recipients' => $recipients],
            'expectedException' => new InvalidMailConfigurationException('The template must be a string.', 1795120003),
        ];
        yield 'key is no valid template folder' => [
            'key' => 'paid-mail',
            'configuration' => ['from' => '*', 'to' => 'paid', 'recipients' => $recipients],
            'expectedException' => new InvalidMailConfigurationException('The key is no valid template folder, it must match /^[a-zA-Z][a-zA-Z0-9]*$/, or set "template".', 1795120004),
        ];
        yield 'missing recipients' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => 'paid'],
            'expectedException' => new InvalidMailConfigurationException('The "recipients" are missing, e.g. "recipients.buyer".', 1795120005),
        ];
        yield 'unknown recipient' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => 'paid', 'recipients' => ['customer' => []]],
            'expectedException' => new InvalidMailConfigurationException('The recipients "customer" are unknown, allowed are "buyer", "seller".', 1795120006),
        ];
        yield 'settings of a recipient are no array' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => 'paid', 'recipients' => ['buyer' => '1']],
            'expectedException' => new InvalidMailConfigurationException('The settings of the recipient "buyer" must be an array.', 1795120007),
        ];
        yield 'seller without toAddress' => [
            'key' => 'paid',
            'configuration' => ['from' => '*', 'to' => 'paid', 'recipients' => ['seller' => ['fromAddress' => 'shop@example.com']]],
            'expectedException' => new InvalidMailConfigurationException('The seller has no "toAddress".', 1795110002),
        ];
    }
}
