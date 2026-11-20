<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\EventListener\Order\StatusChange\Mail;
use Extcode\Cart\OrderStatus\PaymentStatus;
use Extcode\Cart\OrderStatus\ShippingStatus;
use Extcode\Cart\OrderStatus\StatusChangeService;
use Extcode\Cart\Tests\Functional\Mail\CollectingTransport;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

/**
 * Emails of status changes configured in the site settings
 * extcode.cart.statusChange.mail of the site of the order, sent when an editor
 * changes the status in the order details.
 */
#[CoversClass(Mail::class)]
final class StatusChangeMailTest extends AbstractOrderStatusTestCase
{
    private const PAYMENT_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Payment_update';

    private const SHIPPING_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Shipping_update';

    /**
     * Page uid of the root page of the order storage, see Tests/Fixtures/BaseDatabase.php
     */
    private const SHOP_ROOT_PID = 101;

    /**
     * The addresses of the site set extcode/cart-mail.
     */
    private const MAIL_SETTINGS = [
        'buyer' => [
            'fromName' => 'Shop',
            'fromAddress' => 'shop@example.com',
            'bccAddress' => ['archive@example.com'],
        ],
        'seller' => [
            'fromAddress' => 'shop@example.com',
            'toAddress' => ['orders@example.com'],
        ],
    ];

    protected array $configurationToUseInTestInstance = [
        'MAIL' => [
            'transport' => CollectingTransport::class,
            'templateRootPaths' => [
                1795110000 => 'EXT:cart/Tests/Functional/Fixtures/Templates/',
            ],
        ],
        'SYS' => [
            'features' => [
                Mail::FEATURE => true,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CollectingTransport::reset();
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => 'customer@example.com'],
            ['uid' => 1]
        );
    }

    #[Test]
    public function aConfiguredTransitionSendsTheEmailToTheBuyer(): void
    {
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame(['shop@example.com'], self::getAddresses($emails[0]->getFrom()));
        self::assertSame('Shop', $emails[0]->getFrom()[0]->getName());
        self::assertSame(['archive@example.com'], self::getAddresses($emails[0]->getBcc()));
        self::assertSame('Payment/OpenToPaid/Buyer O-001', $emails[0]->getSubject());
        self::assertStringContainsString('Payment/OpenToPaid/Buyer: open to paid', (string) $emails[0]->getHtmlBody());
        self::assertStringContainsString('The payment was successfully updated.', $html);
        self::assertStringNotContainsString('[ERROR]', $html);
        self::assertSame(
            [['info', 'The email "openToPaid" was sent to the buyer.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function anyOldStatusSendsTheEmailsToTheBuyerAndTheSeller(): void
    {
        self::createSite(['payment' => [
            'anyToCanceled' => ['from' => '*', 'to' => 'canceled', 'recipients' => ['buyer', 'seller']],
        ]]);

        self::changePaymentStatus(PaymentStatus::CANCELED);

        $emails = CollectingTransport::getEmails();
        self::assertCount(2, $emails);
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame('Payment/AnyToCanceled/Buyer O-001', $emails[0]->getSubject());
        self::assertSame(['orders@example.com'], self::getAddresses($emails[1]->getTo()));
        self::assertSame(['customer@example.com'], self::getAddresses($emails[1]->getReplyTo()), 'The seller replies to the buyer.');
        self::assertSame('Payment/AnyToCanceled/Seller O-001', $emails[1]->getSubject());
    }

    #[Test]
    public function aListOfOldStatusAndAnExplicitTemplateSendTheEmail(): void
    {
        self::createSite(['shipping' => [
            10 => [
                'from' => ['in_process', 'open'],
                'to' => 'shipped',
                'template' => 'Mail/StatusChange/Shipped',
                'recipients' => ['seller'],
            ],
        ]]);

        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'shippingStatus',
            self::SHIPPING_UPDATE_ROUTE,
            ['status' => ShippingStatus::SHIPPED]
        );

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame('Shipped/Seller O-001', $emails[0]->getSubject());
    }

    #[Test]
    public function theAttachmentsOfTheAttachmentEventAreSent(): void
    {
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);
        // attachments of the emails to the buyer, read by the listener AttachmentFromTypoScript
        $this->getConnectionPool()->getConnectionForTable('sys_template')->insert(
            'sys_template',
            [
                'uid' => 1,
                'pid' => self::SHOP_ROOT_PID,
                'title' => 'Shop',
                'root' => 1,
                'clear' => 3,
                'config' => 'plugin.tx_cart.mail.buyer.attachments.1 = EXT:cart/Tests/Functional/Fixtures/Templates/Mail/StatusChange/Shipped/Seller.html',
            ]
        );

        self::changePaymentStatus(PaymentStatus::PAID);

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        $attachments = $emails[0]->getAttachments();
        self::assertCount(1, $attachments);
        self::assertSame('Seller.html', $attachments[0]->getFilename());
    }

    #[Test]
    public function noEmailIsSentWithoutTheFeatureToggle(): void
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS'];
        self::assertIsArray($configuration);
        $GLOBALS['TYPO3_CONF_VARS'] = ArrayUtility::setValueByPath($configuration, ['SYS', 'features', Mail::FEATURE], false);
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
            'invalid' => 1,
        ]]);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PAID, $this->getStoredPaymentStatus());
        self::assertStringNotContainsString('[ERROR]', $html, 'Even an invalid configuration is ignored.');
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function anotherTransitionSendsNoEmail(): void
    {
        self::createSite([
            'payment' => [
                'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
            ],
            'shipping' => [
                'pendingToShipped' => ['from' => 'pending', 'to' => 'shipped', 'recipients' => ['buyer']],
            ],
        ]);

        $html = self::changePaymentStatus(PaymentStatus::PENDING);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringNotContainsString('[ERROR]', $html);
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function aMissingTemplateShowsAnErrorAndTheStatusIsChangedAnyway(): void
    {
        self::createSite(['payment' => [
            'openToPending' => ['from' => 'open', 'to' => 'pending', 'recipients' => ['buyer']],
        ]]);

        $html = self::changePaymentStatus(PaymentStatus::PENDING);

        $message = 'The email "openToPending" to the buyer was not sent: The template "Mail/StatusChange/Payment/OpenToPending/Buyer" is missing.';
        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PENDING, $this->getStoredPaymentStatus());
        self::assertStringContainsString('The payment was successfully updated.', $html);
        self::assertStringContainsString('[ERROR] ' . $message, $html);
        self::assertSame([['error', $message]], $this->getOrderLog());
    }

    #[Test]
    public function anInvalidConfigurationShowsAnError(): void
    {
        self::createSite(['payment' => [
            10 => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email configuration "10" is invalid, no email was sent: The key is no valid template folder, it must match /^[a-zA-Z][a-zA-Z0-9]*$/, or set "template".',
            $html
        );
    }

    #[Test]
    public function anOrderWithoutEmailOfTheBuyerShowsAnError(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => ''],
            ['uid' => 1]
        );
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);

        $html = self::changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email "openToPaid" to the buyer was not sent: The order has no email address of the buyer.',
            $html
        );
    }

    #[Test]
    public function anInvalidEmailAddressOfTheBuyerShowsAnErrorAndTheStatusIsChangedAnyway(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_address')->update(
            'tx_cart_domain_model_order_address',
            ['email' => 'customer'],
            ['uid' => 1]
        );
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(PaymentStatus::PAID, $this->getStoredPaymentStatus());
        self::assertStringContainsString(
            '[ERROR] The email "openToPaid" to the buyer was not sent: The email address "customer" is invalid.',
            $html
        );
    }

    #[Test]
    public function anInvalidEmailAddressInTheSiteSettingsShowsAnError(): void
    {
        $mailSettings = self::MAIL_SETTINGS;
        $mailSettings['buyer']['bccAddress'] = ['archive'];
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]], $mailSettings);

        $html = $this->changePaymentStatus(PaymentStatus::PAID);

        self::assertSame([], CollectingTransport::getEmails());
        self::assertStringContainsString(
            '[ERROR] The email configuration "openToPaid" is invalid, no email was sent: The email address "archive" of "bccAddress" is invalid.',
            $html
        );
    }

    #[Test]
    public function aMissingAddressOfTheSellerShowsAnError(): void
    {
        $mailSettings = self::MAIL_SETTINGS;
        unset($mailSettings['seller']['toAddress']);
        self::createSite(['payment' => [
            'anyToCanceled' => ['from' => '*', 'to' => 'canceled', 'recipients' => ['buyer', 'seller']],
        ]], $mailSettings);

        $html = $this->changePaymentStatus(PaymentStatus::CANCELED);

        self::assertCount(1, CollectingTransport::getEmails(), 'The email to the buyer is sent.');
        self::assertStringContainsString(
            '[ERROR] The email configuration "anyToCanceled" is invalid, no email was sent: The setting "extcode.cart.mail.seller.toAddress" of the site is missing.',
            $html
        );
    }

    #[Test]
    public function aChangeWithoutRequestSendsTheEmail(): void
    {
        self::createSite(['payment' => [
            'openToPaid' => ['from' => 'open', 'to' => 'paid', 'recipients' => ['buyer']],
        ]]);
        // e.g. on the command line
        unset($GLOBALS['TYPO3_REQUEST']);

        $result = $this->changePaymentStatusWithTheService();

        self::assertTrue($result);
        self::assertCount(1, CollectingTransport::getEmails());
        self::assertSame(
            [['info', 'The email "openToPaid" was sent to the buyer.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function anOrderInNoSiteSendsNoEmailAndLogsAWarning(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);

        $result = $this->changePaymentStatusWithTheService();

        self::assertTrue($result);
        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(
            [['warning', 'No emails of status changes are sent, because the storage page of the order is in no site.']],
            $this->getOrderLog()
        );
    }

    /**
     * Creates the site of the shop with the email rules and the addresses.
     *
     * @param array<string, array<array-key, mixed>> $rules the rules of extcode.cart.statusChange.mail per service type
     * @param array<string, array<string, mixed>> $mailSettings the addresses of extcode.cart.mail
     */
    private static function createSite(array $rules, array $mailSettings = self::MAIL_SETTINGS): void
    {
        $siteWriter = GeneralUtility::makeInstance(SiteWriter::class);
        $siteWriter->createNewBasicSite('shop', self::SHOP_ROOT_PID, 'https://example.com/', ['extcode/cart-mail']);
        $siteWriter->writeSettings('shop', [
            'extcode' => [
                'cart' => [
                    'mail' => $mailSettings,
                    'statusChange' => ['mail' => $rules],
                ],
            ],
        ]);
        // reads the sites without cache, as the site of the previous test is cached
        GeneralUtility::makeInstance(SiteFinder::class)->getAllSites(false);
    }

    /**
     * Changes the payment status to paid without the backend module, e.g. like a command.
     *
     * @return bool whether the status was changed
     */
    private function changePaymentStatusWithTheService(): bool
    {
        $payment = GeneralUtility::makeInstance(PersistenceManager::class)->getObjectByIdentifier(1, Payment::class);
        self::assertInstanceOf(Payment::class, $payment);
        $statusChangeService = $this->get(StatusChangeService::class);
        self::assertInstanceOf(StatusChangeService::class, $statusChangeService);

        return $statusChangeService->change($payment, PaymentStatus::PAID, PaymentStatus::OPEN)->isChanged();
    }

    /**
     * @return string the HTML of the order details after the change
     */
    private static function changePaymentStatus(string $status): string
    {
        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'paymentStatus',
            self::PAYMENT_UPDATE_ROUTE,
            ['status' => $status]
        );

        return (string) self::dispatchShowAction(1)->getBody();
    }

    private function getStoredPaymentStatus(): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_payment')
            ->select(['status'], 'tx_cart_domain_model_order_payment', ['uid' => 1])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }

    /**
     * @return list<array{mixed, mixed}> level and message of the entries of the status change emails
     */
    private function getOrderLog(): array
    {
        $rows = $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_log')
            ->select(['log_level', 'message'], 'tx_cart_domain_model_order_log', ['item' => 1, 'type' => 'statusChangeMail'], [], ['uid' => 'ASC'])
            ->fetchAllAssociative()
        ;

        return array_map(static fn (array $row): array => [$row['log_level'], $row['message']], $rows);
    }

    /**
     * @param array<Address> $addresses
     *
     * @return list<string>
     */
    private static function getAddresses(array $addresses): array
    {
        return array_values(array_map(static fn (Address $address): string => $address->getAddress(), $addresses));
    }
}
