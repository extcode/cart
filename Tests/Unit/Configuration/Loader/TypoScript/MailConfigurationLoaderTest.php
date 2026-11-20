<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Configuration\Loader\TypoScript;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\TypoScript\MailConfigurationLoader;
use Extcode\Cart\Mail\InvalidMailConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(MailConfigurationLoader::class)]
final class MailConfigurationLoaderTest extends UnitTestCase
{
    #[Test]
    public function buyerMailConfigurationIsReadFromMail(): void
    {
        $subject = self::createSubject([
            'mail' => [
                'buyer' => [
                    'fromName' => 'Shop',
                    'fromAddress' => 'shop@example.com',
                    'ccAddress' => 'cc1@example.com, cc2@example.com',
                    'bccAddress' => 'bcc@example.com',
                    'replyToAddress' => 'reply@example.com',
                ],
            ],
        ]);

        $mailConfiguration = $subject->getBuyerMailConfiguration('Mail/Open/Buyer');

        self::assertSame('Mail/Open/Buyer', $mailConfiguration->template);
        self::assertSame('Shop', $mailConfiguration->fromName);
        self::assertSame('shop@example.com', $mailConfiguration->fromAddress);
        self::assertSame(['cc1@example.com', 'cc2@example.com'], $mailConfiguration->cc);
        self::assertSame(['bcc@example.com'], $mailConfiguration->bcc);
        self::assertSame(['reply@example.com'], $mailConfiguration->replyTo);
    }

    #[Test]
    public function settingsOfThePluginOverrideMail(): void
    {
        $subject = self::createSubject([
            'settings' => [
                'buyer' => [
                    'emailFromAddress' => 'plugin@example.com',
                    'emailCcAddress' => '',
                ],
            ],
            'mail' => [
                'buyer' => [
                    'fromName' => 'Shop',
                    'fromAddress' => 'shop@example.com',
                    'ccAddress' => 'cc@example.com',
                ],
            ],
        ]);

        $mailConfiguration = $subject->getBuyerMailConfiguration('Mail/Open/Buyer');

        self::assertSame('plugin@example.com', $mailConfiguration->fromAddress);
        self::assertSame('Shop', $mailConfiguration->fromName, 'An empty setting of the plugin does not override mail.');
        self::assertSame(['cc@example.com'], $mailConfiguration->cc, 'An empty setting of the plugin does not override mail.');
    }

    #[Test]
    public function buyerMailConfigurationWithoutConfigurationHasNoSender(): void
    {
        $mailConfiguration = self::createSubject([])->getBuyerMailConfiguration('Mail/Open/Buyer');

        self::assertSame('', $mailConfiguration->fromAddress);
    }

    #[Test]
    public function sellerMailConfigurationIsReadFromMailAndSettings(): void
    {
        $subject = self::createSubject([
            'settings' => [
                'seller' => [
                    'emailToAddress' => 'orders@example.com, archive@example.com',
                ],
            ],
            'mail' => [
                'seller' => [
                    'fromAddress' => 'shop@example.com',
                    'toAddress' => 'ignored@example.com',
                    'bccAddress' => 'bcc@example.com',
                ],
            ],
        ]);

        $mailConfiguration = $subject->getSellerMailConfiguration('Mail/Open/Seller');

        self::assertSame('Mail/Open/Seller', $mailConfiguration->template);
        self::assertSame(['orders@example.com', 'archive@example.com'], $mailConfiguration->to);
        self::assertSame('shop@example.com', $mailConfiguration->fromAddress);
        self::assertSame(['bcc@example.com'], $mailConfiguration->bcc);
    }

    #[Test]
    public function sellerMailConfigurationWithoutToAddressThrowsException(): void
    {
        $subject = self::createSubject([
            'mail' => [
                'seller' => [
                    'fromAddress' => 'shop@example.com',
                ],
            ],
        ]);

        $this->expectExceptionObject(new InvalidMailConfigurationException('The seller has no "toAddress".', 1795110002));

        $subject->getSellerMailConfiguration('Mail/Open/Seller');
    }

    #[Test]
    public function invalidEmailAddressThrowsException(): void
    {
        $subject = self::createSubject([
            'mail' => [
                'buyer' => [
                    'fromAddress' => 'shop@example.com',
                    'bccAddress' => 'no-email-address',
                ],
            ],
        ]);

        $this->expectExceptionObject(new InvalidMailConfigurationException(
            'The email address "no-email-address" of "bccAddress" is invalid.',
            1795130001
        ));

        $subject->getBuyerMailConfiguration('Mail/Open/Buyer');
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private static function createSubject(array $configuration): MailConfigurationLoader
    {
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);
        $configurationManager->method('getConfiguration')->willReturn($configuration);

        return new MailConfigurationLoader($configurationManager);
    }
}
