<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Mail\AbstractMailConfiguration;
use Extcode\Cart\Mail\BuyerMailConfiguration;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use Extcode\Cart\Mail\MailEnvelope;
use Extcode\Cart\Mail\MailException;
use Extcode\Cart\Mail\SellerMailConfiguration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AbstractMailConfiguration::class)]
#[CoversClass(BuyerMailConfiguration::class)]
#[CoversClass(SellerMailConfiguration::class)]
final class MailConfigurationTest extends UnitTestCase
{
    #[Test]
    public function theBuyerGetsTheEmailAtTheAddressOfTheBillingAddress(): void
    {
        $mailConfiguration = BuyerMailConfiguration::fromArray('Mail/Buyer', [
            'fromName' => ' Shop ',
            'fromAddress' => 'shop@example.com',
            'ccAddress' => 'cc1@example.com, cc2@example.com',
            'bccAddress' => 'archive@example.com,',
            'replyToAddress' => 'service@example.com',
            'nested' => ['ignored'],
        ]);

        self::assertSame('buyer', $mailConfiguration->getRecipientName());
        self::assertEquals(
            new MailEnvelope(
                'shop@example.com',
                'Shop',
                ['customer@example.com'],
                ['cc1@example.com', 'cc2@example.com'],
                ['archive@example.com'],
                ['service@example.com']
            ),
            $mailConfiguration->getEnvelope(self::createOrderItem('customer@example.com'))
        );
    }

    #[Test]
    public function theBuyerGetsNoEmailWithoutAddress(): void
    {
        $this->expectExceptionObject(new MailException('The order has no email address of the buyer.', 1795110001));

        BuyerMailConfiguration::fromArray('Mail/Buyer', [])->getEnvelope(self::createOrderItem(''));
    }

    #[Test]
    public function theSellerGetsTheEmailAtTheToAddressAndRepliesToTheBuyer(): void
    {
        $mailConfiguration = SellerMailConfiguration::fromArray('Mail/Seller', [
            'fromAddress' => 'shop@example.com',
            'toAddress' => 'orders@example.com, owner@example.com',
        ]);

        self::assertSame('seller', $mailConfiguration->getRecipientName());
        self::assertEquals(
            new MailEnvelope('shop@example.com', '', ['orders@example.com', 'owner@example.com'], [], [], ['customer@example.com']),
            $mailConfiguration->getEnvelope(self::createOrderItem('customer@example.com'))
        );
        self::assertSame(
            [],
            $mailConfiguration->getEnvelope(self::createOrderItem(''))->replyTo,
            'Without email address of the buyer there is no reply-to.'
        );
    }

    /**
     * @param 'buyer'|'seller' $recipient
     * @param array<string, string> $settings
     */
    #[DataProvider('invalidAddressDataProvider')]
    #[Test]
    public function fromArrayThrowsExceptionForAnInvalidEmailAddress(string $recipient, array $settings, string $expectedMessage): void
    {
        $this->expectExceptionObject(new InvalidMailConfigurationException($expectedMessage, 1795130001));

        $recipient === 'buyer'
            ? BuyerMailConfiguration::fromArray('Mail/Buyer', $settings)
            : SellerMailConfiguration::fromArray('Mail/Seller', $settings);
    }

    /**
     * @return iterable<string, array{recipient: 'buyer'|'seller', settings: array<string, string>, expectedMessage: string}>
     */
    public static function invalidAddressDataProvider(): iterable
    {
        yield 'fromAddress' => [
            'recipient' => 'buyer',
            'settings' => ['fromAddress' => 'shop'],
            'expectedMessage' => 'The email address "shop" of "fromAddress" is invalid.',
        ];
        yield 'ccAddress' => [
            'recipient' => 'buyer',
            'settings' => ['ccAddress' => 'cc@example.com, cc@'],
            'expectedMessage' => 'The email address "cc@" of "ccAddress" is invalid.',
        ];
        yield 'bccAddress' => [
            'recipient' => 'seller',
            'settings' => ['toAddress' => 'orders@example.com', 'bccAddress' => 'archive example.com'],
            'expectedMessage' => 'The email address "archive example.com" of "bccAddress" is invalid.',
        ];
        yield 'replyToAddress' => [
            'recipient' => 'buyer',
            'settings' => ['replyToAddress' => '@example.com'],
            'expectedMessage' => 'The email address "@example.com" of "replyToAddress" is invalid.',
        ];
        yield 'toAddress' => [
            'recipient' => 'seller',
            'settings' => ['toAddress' => 'orders@example.com, owner'],
            'expectedMessage' => 'The email address "owner" of "toAddress" is invalid.',
        ];
    }

    #[Test]
    public function theBuyerGetsNoEmailWithAnInvalidAddress(): void
    {
        $this->expectExceptionObject(new MailException('The email address "customer" is invalid.', 1795130002));

        BuyerMailConfiguration::fromArray('Mail/Buyer', [])->getEnvelope(self::createOrderItem('customer'));
    }

    private static function createOrderItem(string $buyerAddress): Item
    {
        $billingAddress = new BillingAddress();
        $billingAddress->setEmail($buyerAddress);
        $orderItem = new Item();
        $orderItem->setBillingAddress($billingAddress);

        return $orderItem;
    }
}
