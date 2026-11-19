<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\PhpDataSet;
use Codappix\Typo3PhpDatasets\TestingFramework;
use DateTimeImmutable;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\OrderHistory\OrderChangeSource;
use Extcode\Cart\OrderHistory\OrderChangeType;
use Extcode\Cart\OrderStatus\PaymentStatus;
use Extcode\Cart\OrderStatus\ShippingStatus;
use Extcode\Cart\OrderStatus\StatusChangeResult;
use Extcode\Cart\OrderStatus\StatusChangeService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(StatusChangeService::class)]
final class StatusChangeServiceTest extends FunctionalTestCase
{
    use TestingFramework;

    private const NOW = '2026-11-19 10:30:00';

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private StatusChangeService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BackendUser.php');
        self::createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);

        GeneralUtility::makeInstance(Context::class)->setAspect(
            'date',
            new DateTimeAspect(new DateTimeImmutable(self::NOW))
        );

        $subject = $this->get(StatusChangeService::class);
        self::assertInstanceOf(StatusChangeService::class, $subject);
        $this->subject = $subject;
    }

    #[Test]
    public function changeChangesThePaymentStatusAndWritesTheChangeToTheOrderHistory(): void
    {
        $payment = self::getPayment(1);

        $result = $this->subject->change($payment, PaymentStatus::PAID, PaymentStatus::OPEN);

        self::assertSame(StatusChangeResult::Changed, $result);
        self::assertSame(PaymentStatus::PAID, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame(PaymentStatus::PAID, $payment->getStatus(), 'The new status must be set to the payment.');
        self::assertSame(
            [
                [
                    'item' => 1,
                    'type' => OrderChangeType::PAYMENT_STATUS,
                    'subject' => '',
                    'old_value' => PaymentStatus::OPEN,
                    'new_value' => PaymentStatus::PAID,
                    'crdate' => (new DateTimeImmutable(self::NOW))->getTimestamp(),
                    'be_user' => 0,
                    'source' => OrderChangeSource::Cli->value,
                    'initiator' => '',
                ],
            ],
            $this->getStoredOrderChanges()
        );
    }

    #[Test]
    public function changeChangesTheShippingStatus(): void
    {
        $result = $this->subject->change(
            self::getShipping(1),
            ShippingStatus::SHIPPED,
            ShippingStatus::OPEN
        );

        self::assertSame(StatusChangeResult::Changed, $result);
        self::assertSame(ShippingStatus::SHIPPED, $this->getStoredStatus('tx_cart_domain_model_order_shipping', 1));
        self::assertSame(PaymentStatus::OPEN, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame(OrderChangeType::SHIPPING_STATUS, $this->getStoredOrderChanges()[0]['type'] ?? null);
    }

    #[Test]
    public function changeWritesTheBackendUserToTheOrderHistory(): void
    {
        $this->setUpBackendUser(1);

        $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN);

        self::assertSame(1, $this->getStoredOrderChanges()[0]['be_user'] ?? null);
    }

    /**
     * @param null|int $applicationType null for a request without application type, e.g. of a command
     */
    #[DataProvider('sourceDataProvider')]
    #[Test]
    public function changeWritesTheSourceOfTheRequestToTheOrderHistory(?int $applicationType, OrderChangeSource $expectedSource): void
    {
        $request = new ServerRequest('https://localhost/');
        if ($applicationType !== null) {
            $request = $request->withAttribute('applicationType', $applicationType);
        }

        $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN, request: $request);

        self::assertSame($expectedSource->value, $this->getStoredOrderChanges()[0]['source'] ?? null);
    }

    /**
     * @return iterable<string, array{applicationType: null|int, expectedSource: OrderChangeSource}>
     */
    public static function sourceDataProvider(): iterable
    {
        yield 'backend request' => [
            'applicationType' => SystemEnvironmentBuilder::REQUESTTYPE_BE,
            'expectedSource' => OrderChangeSource::Backend,
        ];
        yield 'frontend request' => [
            'applicationType' => SystemEnvironmentBuilder::REQUESTTYPE_FE,
            'expectedSource' => OrderChangeSource::Frontend,
        ];
        yield 'command line without application type' => [
            'applicationType' => null,
            'expectedSource' => OrderChangeSource::Cli,
        ];
    }

    #[Test]
    public function changeWritesTheSourceOfTheGlobalRequestIfNoRequestIsGiven(): void
    {
        // loaded before with its order, Extbase would need the TypoScript of the frontend request otherwise
        $payment = self::getPayment(1);
        self::assertSame(1, $payment->getItem()?->getUid());
        $GLOBALS['TYPO3_REQUEST'] = (new ServerRequest('https://localhost/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
        ;

        try {
            $this->subject->change($payment, PaymentStatus::PAID, PaymentStatus::OPEN);
        } finally {
            unset($GLOBALS['TYPO3_REQUEST']);
        }

        self::assertSame(OrderChangeSource::Frontend->value, $this->getStoredOrderChanges()[0]['source'] ?? null);
    }

    #[Test]
    public function changeWritesTheGivenSourceInsteadOfTheOneOfTheRequest(): void
    {
        // e.g. the notification of a payment provider, which TYPO3 handles as frontend request
        $request = (new ServerRequest('https://localhost/'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
        ;

        $this->subject->change(
            self::getPayment(1),
            PaymentStatus::PAID,
            PaymentStatus::OPEN,
            'cart_paypal',
            $request,
            OrderChangeSource::Api
        );

        self::assertSame(OrderChangeSource::Api->value, $this->getStoredOrderChanges()[0]['source'] ?? null);
    }

    #[Test]
    public function changeWritesTheInitiatorToTheOrderHistory(): void
    {
        $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN, 'cart_paypal');

        self::assertSame('cart_paypal', $this->getStoredOrderChanges()[0]['initiator'] ?? null);
    }

    #[Test]
    public function changeDoesNothingIfTheStatusIsNotChanged(): void
    {
        $result = $this->subject->change(self::getPayment(1), PaymentStatus::OPEN, PaymentStatus::OPEN);

        self::assertSame(StatusChangeResult::Unchanged, $result);
        self::assertSame(PaymentStatus::OPEN, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame([], $this->getStoredOrderChanges());
    }

    #[Test]
    public function changeDoesNothingIfTheStatusIsNotRegistered(): void
    {
        $payment = self::getPayment(1);

        $result = $this->subject->change($payment, 'not_registered', PaymentStatus::OPEN);

        self::assertSame(StatusChangeResult::UnknownStatus, $result);
        self::assertSame(PaymentStatus::OPEN, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame(PaymentStatus::OPEN, $payment->getStatus());
        self::assertSame([], $this->getStoredOrderChanges());
    }

    #[Test]
    public function changeDoesNothingIfTheOrderHasNotTheExpectedStatusAnymore(): void
    {
        // e.g. changed in another tab
        $this->setStoredStatus('tx_cart_domain_model_order_payment', 1, PaymentStatus::PENDING);

        $result = $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN);

        self::assertSame(StatusChangeResult::Stale, $result);
        self::assertSame(PaymentStatus::PENDING, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame([], $this->getStoredOrderChanges());
    }

    #[Test]
    public function changeChangesTheStatusOnlyOnceForTheSameExpectedStatus(): void
    {
        // e.g. a form submitted twice
        $first = $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN);
        $second = $this->subject->change(self::getPayment(1), PaymentStatus::CANCELED, PaymentStatus::OPEN);
        $third = $this->subject->change(self::getPayment(1), PaymentStatus::PAID, PaymentStatus::OPEN);

        self::assertSame(StatusChangeResult::Changed, $first);
        self::assertSame(StatusChangeResult::Stale, $second, 'Another status must not overwrite the changed status.');
        self::assertSame(StatusChangeResult::Unchanged, $third, 'The same status again must not be reported as stale.');
        self::assertSame(PaymentStatus::PAID, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertCount(1, $this->getStoredOrderChanges());
    }

    #[Test]
    public function changeThrowsExceptionForAPaymentWithoutOrder(): void
    {
        $this->expectExceptionObject(
            new InvalidArgumentException('The status can only be changed for persisted orders.', 1795089001)
        );

        $this->subject->change(new Payment(), PaymentStatus::PAID, PaymentStatus::OPEN);
    }

    private static function getPayment(int $uid): Payment
    {
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->clearState();
        $payment = $persistenceManager->getObjectByIdentifier($uid, Payment::class);
        self::assertInstanceOf(Payment::class, $payment);

        return $payment;
    }

    private static function getShipping(int $uid): Shipping
    {
        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $persistenceManager->clearState();
        $shipping = $persistenceManager->getObjectByIdentifier($uid, Shipping::class);
        self::assertInstanceOf(Shipping::class, $shipping);

        return $shipping;
    }

    private function getStoredStatus(string $table, int $uid): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable($table)
            ->select(['status'], $table, ['uid' => $uid])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }

    private function setStoredStatus(string $table, int $uid, string $status): void
    {
        $this->getConnectionPool()->getConnectionForTable($table)->update($table, ['status' => $status], ['uid' => $uid]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getStoredOrderChanges(): array
    {
        $table = 'tx_cart_domain_model_order_change';

        return $this->getConnectionPool()->getConnectionForTable($table)
            ->select(['item', 'type', 'subject', 'old_value', 'new_value', 'crdate', 'be_user', 'source', 'initiator'], $table, [], [], ['uid' => 'ASC'])
            ->fetchAllAssociative()
        ;
    }

    private static function createOrder(int $uid, string $paymentStatus, string $shippingStatus): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                ['uid' => $uid, 'pid' => 105, 'payment' => 1, 'shipping' => 1, 'order_number' => 'O-' . $uid],
            ],
            'tx_cart_domain_model_order_payment' => [
                ['uid' => $uid, 'pid' => 105, 'item' => $uid, 'name' => 'Payment ' . $uid, 'status' => $paymentStatus],
            ],
            'tx_cart_domain_model_order_shipping' => [
                ['uid' => $uid, 'pid' => 105, 'item' => $uid, 'name' => 'Shipping ' . $uid, 'status' => $shippingStatus],
            ],
        ]);
    }
}
