<?php

namespace Extcode\Cart\Tests\Functional\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Exception;
use Extcode\Cart\Domain\Log\DatabaseWriter;
use Extcode\Cart\Domain\Log\LogService;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Service\MailHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\MockObject\Stub;
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
        $this->testExtensionsToLoad = [
            'extcode/cart',
            'typo3conf/ext/cart/Tests/Fixtures/cart_example',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
            'typo3/cms-core',
        ];

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
    public function logSucessAfterEmailToBuyerWasSend(): void
    {
        $mockBuilder = $this->getMockBuilderForMailHandlerClass();
        $mockBuilder->onlyMethods(
            [
                'getBuyerEmailFrom',
                'getBuyerEmailName',
            ]
        );
        $mailHandler = $mockBuilder->getMock();
        $mailHandler->method('getBuyerEmailFrom')->willReturn('buyerEmailFrom@example.com');
        $mailHandler->method('getBuyerEmailName')->willReturn('Buyer Email Name');

        $mailHandler->sendBuyerMail(
            $this->createStubForOrderItem()
        );

        $logEntries = $this->getAllRecords('tx_cart_domain_model_order_log');
        self::assertCount(
            1,
            $logEntries
        );
        self::assertIsArray($logEntries[0]);

        self::assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                'log_level' => 'info',
                'item' => 142,
                'type' => 'sendBuyerMail',
                'message' => 'Mail was send to buyer.',
                'level' => 'info',
            ],
            $logEntries[0],
            [
                'uid',
                'request_id',
                'crdate',
                'time_micro',
                'data',
                'arguments',
            ]
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

    #[Test]
    public function logErrorIfEmailToBuyerCouldNotSend(): void
    {
        $mockBuilder = $this->getMockBuilderForMailHandlerClass(mailerThrowException: true);
        $mockBuilder->onlyMethods(
            [
                'getBuyerEmailFrom',
                'getBuyerEmailName',
            ]
        );
        $mailHandler = $mockBuilder->getMock();
        $mailHandler->method('getBuyerEmailFrom')->willReturn('buyerEmailFrom@example.com');
        $mailHandler->method('getBuyerEmailName')->willReturn('Buyer Email Name');

        $mailHandler->sendBuyerMail(
            $this->createStubForOrderItem()
        );

        $logEntries = $this->getAllRecords('tx_cart_domain_model_order_log');
        self::assertCount(
            1,
            $logEntries
        );
        self::assertIsArray($logEntries[0]);

        self::assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                'log_level' => 'error',
                'item' => 142,
                'type' => 'sendBuyerMail',
                'message' => 'Mail could not send to buyer.',
                'level' => 'error',
            ],
            $logEntries[0],
            [
                'uid',
                'request_id',
                'crdate',
                'time_micro',
                'data',
                'arguments',
            ]
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
        self::assertArrayHasKey(
            'exception',
            $arguments
        );
        self::assertIsString(
            $arguments['exception']
        );
        self::assertStringStartsWith(
            'Exception in ',
            $arguments['exception']
        );
        self::assertStringContainsString(
            '/cart/Tests/Functional/Service/MailHandlerTest.php',
            $arguments['exception']
        );
    }

    #[Test]
    public function logSucessAfterEmailToSellerWasSend(): void
    {
        $mockBuilder = $this->getMockBuilderForMailHandlerClass();
        $mockBuilder->onlyMethods(
            [
                'getSellerEmailTo',
                'getSellerEmailFrom',
                'getSellerEmailName',
            ]
        );
        $mailHandler = $mockBuilder->getMock();
        $mailHandler->method('getSellerEmailTo')->willReturn('sellerEmailTo@example.com');
        $mailHandler->method('getSellerEmailFrom')->willReturn('sellerEmailFrom@example.com');
        $mailHandler->method('getSellerEmailName')->willReturn('Seller Email Name');

        $mailHandler->sendSellerMail(
            $this->createStubForOrderItem()
        );

        $logEntries = $this->getAllRecords('tx_cart_domain_model_order_log');
        self::assertCount(
            1,
            $logEntries
        );
        self::assertIsArray($logEntries[0]);

        self::assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                'log_level' => 'info',
                'item' => 142,
                'type' => 'sendSellerMail',
                'message' => 'Mail was send to seller.',
                'level' => 'info',
            ],
            $logEntries[0],
            [
                'uid',
                'request_id',
                'crdate',
                'time_micro',
                'data',
                'arguments',
            ]
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

    #[Test]
    public function logErrorIfEmailToSellerCouldNotSend(): void
    {
        $mockBuilder = $this->getMockBuilderForMailHandlerClass(mailerThrowException: true);
        $mockBuilder->onlyMethods(
            [
                'getSellerEmailTo',
                'getSellerEmailFrom',
                'getSellerEmailName',
            ]
        );
        $mailHandler = $mockBuilder->getMock();
        $mailHandler->method('getSellerEmailTo')->willReturn('sellerEmailTo@example.com');
        $mailHandler->method('getSellerEmailFrom')->willReturn('sellerEmailFrom@example.com');
        $mailHandler->method('getSellerEmailName')->willReturn('Seller Email Name');

        $mailHandler->sendSellerMail(
            $this->createStubForOrderItem()
        );

        $logEntries = $this->getAllRecords('tx_cart_domain_model_order_log');
        self::assertCount(
            1,
            $logEntries
        );
        self::assertIsArray($logEntries[0]);

        self::assertArrayIsEqualToArrayIgnoringListOfKeys(
            [
                'log_level' => 'error',
                'item' => 142,
                'type' => 'sendSellerMail',
                'message' => 'Mail could not send to seller.',
                'level' => 'error',
            ],
            $logEntries[0],
            [
                'uid',
                'request_id',
                'crdate',
                'time_micro',
                'data',
                'arguments',
            ]
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
        self::assertArrayHasKey(
            'exception',
            $arguments
        );
        self::assertIsString(
            $arguments['exception']
        );
        self::assertStringStartsWith(
            'Exception in ',
            $arguments['exception']
        );
        self::assertStringContainsString(
            '/cart/Tests/Functional/Service/MailHandlerTest.php',
            $arguments['exception']
        );
    }

    /**
     * @return MockBuilder<MailHandler>
     */
    private function getMockBuilderForMailHandlerClass(bool $mailerThrowException = false): MockBuilder
    {
        $configurationManager = self::createStub(ConfigurationManagerInterface::class);

        $eventDispatcher = self::createStub(EventDispatcherInterface::class);

        $mailer = self::createStub(MailerInterface::class);
        if ($mailerThrowException) {
            $mailer->method('send')->willThrowException(new Exception());
        }

        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(self::class);
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

        return $mockBuilder;
    }

    private function createStubForOrderItem(): OrderItem&Stub
    {
        $billingAddress = self::createStub(BillingAddress::class);
        $billingAddress->method('getEmail')->willReturn('billingAddress@example.com');

        $payment = self::createStub(Payment::class);
        $payment->method('getStatus')->willReturn('open');

        $orderItem = self::createStub(OrderItem::class);
        $orderItem->method('getBillingAddress')->willReturn($billingAddress);
        $orderItem->method('getPayment')->willReturn($payment);
        $orderItem->method('getUid')->willReturn(142);

        return $orderItem;
    }

}
