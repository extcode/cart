<?php

namespace Extcode\Cart\Tests\Functional\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Log\DatabaseWriter;
use Extcode\Cart\Domain\Log\LogService;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Service\MailHandler;
use PHPUnit\Framework\Attributes\Test;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class MailHandlerTest extends FunctionalTestCase
{
    use TestingFramework;

    public function setUp(): void
    {
        $this->testExtensionsToLoad[] = 'extcode/cart';
        $this->testExtensionsToLoad[] = 'typo3conf/ext/cart/Tests/Fixtures/cart_example';

        $this->configurationToUseInTestInstance = [
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
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
    }

    #[Test]
    public function logSucessAfterEmailWasSend(): void
    {
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);

        $eventDispatcher = self::createStub(EventDispatcherInterface::class);

        $mailer = self::createStub(MailerInterface::class);

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $logService = GeneralUtility::makeInstance(
            LogService::class,
            $logger,
        );

        $mockBuilder = $this->getMockBuilder(MailHandler::class);
        $mockBuilder->setConstructorArgs(
            [
                $configurationManager,
                $eventDispatcher,
                $mailer,
                $logService,
            ]
        );
        $mockBuilder->onlyMethods(
            [
                'getBuyerEmailFrom',
                'getBuyerEmailName',
            ]
        );
        $mailHandler = $mockBuilder->getMock();
        $mailHandler->method('getBuyerEmailFrom')->willReturn('buyerEmailFrom@example.com');
        $mailHandler->method('getBuyerEmailName')->willReturn('Buyer Email Name');

        $billingAddress = self::createStub(BillingAddress::class);
        $billingAddress->method('getEmail')->willReturn('billingAddress@example.com');

        $payment = self::createStub(Payment::class);
        $payment->method('getStatus')->willReturn('open');
        $orderItem = self::createStub(OrderItem::class);
        $orderItem->method('getBillingAddress')->willReturn($billingAddress);
        $orderItem->method('getPayment')->willReturn($payment);
        $orderItem->method('getUid')->willReturn(142);

        $mailHandler->sendBuyerMail(
            $orderItem
        );

        $logEntries = $this->getAllRecords('tx_cart_domain_model_order_log');
        self::assertCount(
            1,
            $logEntries
        );
        self::assertIsArray($logEntries[0]);
        self::assertArrayHasKey(
            'level',
            $logEntries[0]
        );
        self::assertSame(
            'info',
            $logEntries[0]['level']
        );
        self::assertArrayHasKey(
            'identifier',
            $logEntries[0]
        );
        self::assertSame(
            '142',
            $logEntries[0]['identifier']
        );
        self::assertArrayHasKey(
            'message',
            $logEntries[0]
        );
        self::assertSame(
            'Mail was send to buyer.',
            $logEntries[0]['message']
        );
        self::assertArrayHasKey(
            'arguments',
            $logEntries[0]
        );
        self::assertIsString(
            $logEntries[0]['arguments']
        );
        $arguments = json_decode(
            ltrim($logEntries[0]['arguments'], '- '),
            true
        );
        self::assertIsArray(
            $arguments
        );
        self::assertArrayHasKey(
            'time',
            $arguments
        );
    }
}
