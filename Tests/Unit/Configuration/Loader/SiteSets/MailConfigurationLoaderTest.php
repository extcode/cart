<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Configuration\Loader\SiteSets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\SiteSets\MailConfigurationLoader;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Settings\Settings;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteSettings;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(MailConfigurationLoader::class)]
final class MailConfigurationLoaderTest extends UnitTestCase
{
    #[Test]
    public function buyerMailConfigurationIsReadFromTheSiteSettings(): void
    {
        $subject = self::createSubject([
            'extcode.cart.mail.buyer.fromName' => 'Shop',
            'extcode.cart.mail.buyer.fromAddress' => 'shop@example.com',
            'extcode.cart.mail.buyer.ccAddress' => ['cc1@example.com', ' cc2@example.com ', ''],
            'extcode.cart.mail.buyer.bccAddress' => ['bcc@example.com'],
            'extcode.cart.mail.buyer.replyToAddress' => ['reply@example.com'],
        ]);

        $mailConfiguration = $subject->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());

        self::assertSame('Mail/Open/Buyer', $mailConfiguration->template);
        self::assertSame('Shop', $mailConfiguration->fromName);
        self::assertSame('shop@example.com', $mailConfiguration->fromAddress);
        self::assertSame(['cc1@example.com', 'cc2@example.com'], $mailConfiguration->cc);
        self::assertSame(['bcc@example.com'], $mailConfiguration->bcc);
        self::assertSame(['reply@example.com'], $mailConfiguration->replyTo);
    }

    #[Test]
    public function buyerMailConfigurationWithoutSettingsHasNoSender(): void
    {
        $subject = self::createSubject([]);

        $mailConfiguration = $subject->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());

        self::assertSame('', $mailConfiguration->fromAddress);
        self::assertSame([], $mailConfiguration->cc);
    }

    #[Test]
    public function sellerMailConfigurationIsReadFromTheSiteSettings(): void
    {
        $subject = self::createSubject([
            'extcode.cart.mail.seller.fromName' => 'Shop',
            'extcode.cart.mail.seller.fromAddress' => 'shop@example.com',
            'extcode.cart.mail.seller.toAddress' => ['orders@example.com', 'archive@example.com'],
            'extcode.cart.mail.seller.bccAddress' => ['bcc@example.com'],
        ]);

        $mailConfiguration = $subject->getSellerMailConfiguration('Mail/Open/Seller', self::createStubForOrderItem());

        self::assertSame('Mail/Open/Seller', $mailConfiguration->template);
        self::assertSame(['orders@example.com', 'archive@example.com'], $mailConfiguration->to);
        self::assertSame('Shop', $mailConfiguration->fromName);
        self::assertSame('shop@example.com', $mailConfiguration->fromAddress);
        self::assertSame([], $mailConfiguration->cc);
        self::assertSame(['bcc@example.com'], $mailConfiguration->bcc);
    }

    #[Test]
    public function sellerMailConfigurationWithoutToAddressThrowsException(): void
    {
        $subject = self::createSubject([
            'extcode.cart.mail.seller.fromAddress' => 'shop@example.com',
            'extcode.cart.mail.seller.toAddress' => [],
        ]);

        $this->expectExceptionObject(new InvalidMailConfigurationException(
            'The setting "extcode.cart.mail.seller.toAddress" of the site is missing.',
            1795150002
        ));

        $subject->getSellerMailConfiguration('Mail/Open/Seller', self::createStubForOrderItem());
    }

    #[Test]
    public function invalidEmailAddressThrowsException(): void
    {
        $subject = self::createSubject([
            'extcode.cart.mail.buyer.fromAddress' => 'shop@example.com',
            'extcode.cart.mail.buyer.bccAddress' => ['no-email-address'],
        ]);

        $this->expectExceptionObject(new InvalidMailConfigurationException(
            'The email address "no-email-address" of "bccAddress" is invalid.',
            1795130001
        ));

        $subject->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());
    }

    #[Test]
    public function storagePageOfTheOrderInNoSiteThrowsException(): void
    {
        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')->willThrowException(new SiteNotFoundException('No site found', 1521716622));
        $subject = new MailConfigurationLoader($siteFinder);

        $this->expectExceptionObject(new InvalidMailConfigurationException('The storage page 105 of the order is in no site.', 1795150001));

        $subject->getBuyerMailConfiguration('Mail/Open/Buyer', self::createStubForOrderItem());
    }

    /**
     * @param array<string, mixed> $settings the flat site settings, e.g. "extcode.cart.mail.buyer.fromAddress"
     */
    private static function createSubject(array $settings): MailConfigurationLoader
    {
        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')->willReturnMap([
            [105, null, null, new Site('main', 1, [], SiteSettings::create(new Settings($settings)))],
        ]);

        return new MailConfigurationLoader($siteFinder);
    }

    private static function createStubForOrderItem(): Item
    {
        $orderItem = self::createStub(Item::class);
        $orderItem->method('getPid')->willReturn(105);

        return $orderItem;
    }
}
