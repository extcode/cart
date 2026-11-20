<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Exception;
use Extcode\Cart\Configuration\Loader\TypoScript\MailConfigurationLoader;
use Extcode\Cart\Domain\Log\DatabaseWriter;
use Extcode\Cart\Domain\Log\LogService;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Event\Mail\AttachmentEvent;
use Extcode\Cart\Mail\Mailer;
use Extcode\Cart\Mail\OrderMailSender;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(OrderMailSender::class)]
final class OrderMailSenderTest extends FunctionalTestCase
{
    use TestingFramework;

    private const CONFIGURATION = [
        'settings' => [
            'shopName' => 'My Shop',
        ],
        'mail' => [
            'buyer' => [
                'fromName' => 'Shop',
                'fromAddress' => 'shop@example.com',
                'bccAddress' => 'archive@example.com',
            ],
            'seller' => [
                'fromAddress' => 'shop@example.com',
                'toAddress' => 'orders@example.com',
            ],
        ],
    ];

    protected array $testExtensionsToLoad = [
        'extcode/cart',
        'typo3conf/ext/cart/Tests/Fixtures/cart_example',
    ];

    protected array $coreExtensionsToLoad = [
        'typo3/cms-beuser',
        'typo3/cms-core',
    ];

    protected array $configurationToUseInTestInstance = [
        'LOG' => [
            'Extcode' => [
                'Cart' => [
                    'Tests' => [
                        'writerConfiguration' => [
                            LogLevel::INFO => [
                                DatabaseWriter::class => [],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'MAIL' => [
            'transport' => CollectingTransport::class,
            'templateRootPaths' => [
                1795110000 => 'EXT:cart/Tests/Functional/Fixtures/Templates/',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
        CollectingTransport::reset();
    }

    #[Test]
    public function sendsTheEmailToTheBuyer(): void
    {
        self::createSubject()->sendBuyerMail(self::createStubForOrderItem());

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame(['shop@example.com'], self::getAddresses($emails[0]->getFrom()));
        self::assertSame('Shop', $emails[0]->getFrom()[0]->getName());
        self::assertSame(['archive@example.com'], self::getAddresses($emails[0]->getBcc()));
        self::assertSame('Open/Buyer O-142', $emails[0]->getSubject());
        self::assertStringContainsString('Open/Buyer: My Shop', (string) $emails[0]->getHtmlBody());
        self::assertSame(
            [['info', 'sendBuyerMail', 'Mail was send to buyer.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function sendsTheEmailToTheSeller(): void
    {
        self::createSubject()->sendSellerMail(self::createStubForOrderItem());

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        self::assertSame(['orders@example.com'], self::getAddresses($emails[0]->getTo()));
        self::assertSame(['customer@example.com'], self::getAddresses($emails[0]->getReplyTo()), 'The seller replies to the buyer.');
        self::assertSame('Open/Seller O-142', $emails[0]->getSubject());
        self::assertSame(
            [['info', 'sendSellerMail', 'Mail was send to seller.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function sendsNoEmailWithoutSenderAddress(): void
    {
        $configuration = self::CONFIGURATION;
        unset($configuration['mail']['buyer']['fromAddress'], $configuration['mail']['seller']['fromAddress']);
        $subject = self::createSubject(configuration: $configuration);

        $subject->sendBuyerMail(self::createStubForOrderItem());
        $subject->sendSellerMail(self::createStubForOrderItem());

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function logsAnErrorWithoutToAddressOfTheSeller(): void
    {
        $configuration = self::CONFIGURATION;
        unset($configuration['mail']['seller']['toAddress']);

        self::createSubject(configuration: $configuration)->sendSellerMail(self::createStubForOrderItem());

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(
            [['error', 'sendSellerMail', 'Mail could not send to seller.']],
            $this->getOrderLog()
        );
    }

    #[Test]
    public function sendsNoEmailToTheBuyerWithoutEmailAddressOfTheBuyer(): void
    {
        self::createSubject()->sendBuyerMail(self::createStubForOrderItem(buyerEmail: ''));

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame([], $this->getOrderLog());
    }

    #[Test]
    public function logsAnErrorForAnInvalidConfiguration(): void
    {
        $configuration = self::CONFIGURATION;
        $configuration['mail']['buyer']['bccAddress'] = 'no-email-address';

        self::createSubject(configuration: $configuration)->sendBuyerMail(self::createStubForOrderItem());

        self::assertSame([], CollectingTransport::getEmails());
        self::assertSame(
            [['error', 'sendBuyerMail', 'Mail could not send to buyer.']],
            $this->getOrderLog()
        );
        $exception = $this->getOrderLogArguments()[0]['exception'] ?? null;
        self::assertIsString($exception);
        self::assertStringContainsString('The email address "no-email-address" of "bccAddress" is invalid.', $exception);
    }

    #[Test]
    public function logsAnErrorIfTheEmailCouldNotBeSent(): void
    {
        $mailer = self::createStub(MailerInterface::class);
        $mailer->method('send')->willThrowException(new Exception('Connection refused'));

        self::createSubject(mailer: $mailer)->sendSellerMail(self::createStubForOrderItem());

        self::assertSame(
            [['error', 'sendSellerMail', 'Mail could not send to seller.']],
            $this->getOrderLog()
        );
        $arguments = $this->getOrderLogArguments()[0];
        self::assertArrayHasKey('time', $arguments);
        self::assertIsString($arguments['exception'] ?? null);
        self::assertStringStartsWith('Exception: Connection refused in ' . __FILE__ . ':', $arguments['exception']);
    }

    #[Test]
    public function attachesTheExistingFilesOfTheAttachmentEvent(): void
    {
        $eventDispatcher = self::createStub(EventDispatcherInterface::class);
        $eventDispatcher->method('dispatch')->willReturnCallback(static function (object $event): object {
            self::assertInstanceOf(AttachmentEvent::class, $event);
            self::assertSame('buyer', $event->getType());
            $event->addAttachment(__FILE__);
            $event->addAttachment('/does/not/exist.pdf');

            return $event;
        });

        self::createSubject(eventDispatcher: $eventDispatcher)->sendBuyerMail(self::createStubForOrderItem());

        $emails = CollectingTransport::getEmails();
        self::assertCount(1, $emails);
        $attachments = $emails[0]->getAttachments();
        self::assertCount(1, $attachments);
        self::assertSame('OrderMailSenderTest.php', $attachments[0]->getFilename());
        self::assertSame(
            [
                ['warning', 'addAttachments', 'Mail could add attachment /does/not/exist.pdf to mail.'],
                ['info', 'sendBuyerMail', 'Mail was send to buyer.'],
            ],
            $this->getOrderLog()
        );
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private static function createSubject(
        array $configuration = self::CONFIGURATION,
        ?MailerInterface $mailer = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ): OrderMailSender {
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);
        $configurationManager->method('getConfiguration')->willReturn($configuration);

        $logService = GeneralUtility::makeInstance(
            LogService::class,
            GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class),
        );

        return new OrderMailSender(
            new MailConfigurationLoader($configurationManager),
            new Mailer($mailer ?? GeneralUtility::makeInstance(MailerInterface::class)),
            $configurationManager,
            $eventDispatcher ?? self::createStub(EventDispatcherInterface::class),
            $logService,
        );
    }

    private static function createStubForOrderItem(string $buyerEmail = 'customer@example.com'): OrderItem&Stub
    {
        $billingAddress = self::createStub(BillingAddress::class);
        $billingAddress->method('getEmail')->willReturn($buyerEmail);

        $payment = self::createStub(Payment::class);
        $payment->method('getStatus')->willReturn('open');

        $orderItem = self::createStub(OrderItem::class);
        $orderItem->method('getBillingAddress')->willReturn($billingAddress);
        $orderItem->method('getPayment')->willReturn($payment);
        $orderItem->method('getUid')->willReturn(142);
        $orderItem->method('getOrderNumber')->willReturn('O-142');

        return $orderItem;
    }

    /**
     * @return list<array{string, string, string}> level, type and message of the order log
     */
    private function getOrderLog(): array
    {
        $log = [];
        foreach ($this->getAllRecords('tx_cart_domain_model_order_log') as $record) {
            self::assertIsArray($record);
            self::assertIsString($record['log_level']);
            self::assertIsString($record['type']);
            self::assertIsString($record['message']);
            $log[] = [$record['log_level'], $record['type'], $record['message']];
        }

        return $log;
    }

    /**
     * @return list<array<array-key, mixed>> the arguments of the order log
     */
    private function getOrderLogArguments(): array
    {
        $arguments = [];
        foreach ($this->getAllRecords('tx_cart_domain_model_order_log') as $record) {
            self::assertIsArray($record);
            self::assertIsString($record['arguments']);
            $decoded = json_decode(mb_ltrim($record['arguments'], '- '), true);
            self::assertIsArray($decoded);
            $arguments[] = $decoded;
        }

        return $arguments;
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
