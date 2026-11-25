<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use ArrayObject;
use Closure;
use Codappix\Typo3PhpDatasets\TestingFramework;
use DateTimeImmutable;
use Extcode\Cart\Domain\Order\Fulfillment\Shipment;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentProduct;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentRepository;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatusChangeResult;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatusChangeService;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\History\OrderChangeType;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Domain\Order\Status\StatusChangeOutcome;
use Extcode\Cart\Domain\Order\Status\StatusTransition;
use Extcode\Cart\Event\Order\AfterShipmentStatusChangeEvent;
use Extcode\Cart\Event\Order\BeforeShipmentStatusChangeEvent;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ShipmentStatusChangeService::class)]
#[CoversClass(ShipmentStatusChangeResult::class)]
#[CoversClass(StatusTransition::class)]
#[CoversClass(BeforeShipmentStatusChangeEvent::class)]
#[CoversClass(AfterShipmentStatusChangeEvent::class)]
final class ShipmentStatusChangeServiceTest extends FunctionalTestCase
{
    use TestingFramework;

    private const NOW = '2026-11-25 10:30:00';

    private const ORDER_SHIPMENT_TABLE_NAME = 'tx_cart_domain_model_order_shipment';

    private const ORDER_SHIPPING_TABLE_NAME = 'tx_cart_domain_model_order_shipping';

    private const ORDER_CHANGE_TABLE_NAME = 'tx_cart_domain_model_order_change';

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private ShipmentStatusChangeService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderWithProducts.php');

        GeneralUtility::makeInstance(Context::class)->setAspect(
            'date',
            new DateTimeAspect(new DateTimeImmutable(self::NOW))
        );

        $subject = $this->get(ShipmentStatusChangeService::class);
        self::assertInstanceOf(ShipmentStatusChangeService::class, $subject);
        $this->subject = $subject;
    }

    #[Test]
    public function changeChangesTheStatusAndWritesTheChangeToTheOrderHistory(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::OPEN, 1);

        $result = $this->subject->change($shipment, ShipmentStatus::IN_PROCESS, ShipmentStatus::OPEN, 'erp_sync', source: OrderChangeSource::Api);

        self::assertTrue($result->isChanged());
        self::assertSame(StatusChangeOutcome::Changed, $result->shipmentStatusChange->outcome);
        self::assertSame(ShipmentStatus::IN_PROCESS, $this->fetchShipmentStatus($shipment->uid));
        self::assertSame(
            [
                [
                    'item' => 1,
                    'type' => OrderChangeType::SHIPMENT_STATUS,
                    'subject' => 'shipment:' . $shipment->uid,
                    'old_value' => ShipmentStatus::OPEN,
                    'new_value' => ShipmentStatus::IN_PROCESS,
                    'crdate' => (new DateTimeImmutable(self::NOW))->getTimestamp(),
                    'source' => 'api',
                    'initiator' => 'erp_sync',
                ],
            ],
            $this->fetchOrderChanges()
        );
        self::assertNull($result->shippingStatusChange, 'Nothing is shipped, the shipping status of the order is kept.');
        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus(1));
    }

    #[Test]
    public function shippingAShipmentChangesTheShippingStatusOfTheOrder(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::IN_PROCESS, 1);

        $result = $this->subject->change($shipment, ShipmentStatus::SHIPPED, ShipmentStatus::IN_PROCESS);

        self::assertSame(StatusChangeOutcome::Changed, $result->shippingStatusChange?->outcome);
        self::assertSame(ShippingStatus::PARTIALLY_SHIPPED, $this->fetchShippingStatus(1));
        self::assertSame(
            [
                [OrderChangeType::SHIPMENT_STATUS, ShipmentStatus::IN_PROCESS, ShipmentStatus::SHIPPED],
                [OrderChangeType::SHIPPING_STATUS, ShippingStatus::OPEN, ShippingStatus::PARTIALLY_SHIPPED],
            ],
            array_map(
                static fn (array $orderChange): array => [$orderChange['type'], $orderChange['old_value'], $orderChange['new_value']],
                $this->fetchOrderChanges()
            )
        );
    }

    #[Test]
    public function settingBackTheOnlyShippedShipmentChangesTheShippingStatusOfTheOrderToInProcess(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::SHIPPED, 3);
        $this->setShippingStatus(1, ShippingStatus::PARTIALLY_SHIPPED);

        $result = $this->subject->change($shipment, ShipmentStatus::IN_PROCESS, ShipmentStatus::SHIPPED);

        self::assertSame(StatusChangeOutcome::Changed, $result->shippingStatusChange?->outcome);
        self::assertSame(ShippingStatus::IN_PROCESS, $this->fetchShippingStatus(1));
    }

    #[Test]
    public function cancelingAShipmentBeforeItIsShippedFreesItsCount(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::IN_PROCESS, 3);

        $result = $this->subject->change($shipment, ShipmentStatus::CANCELED, ShipmentStatus::IN_PROCESS);

        self::assertTrue($result->isChanged());
        self::assertSame(ShipmentStatus::CANCELED, $this->fetchShipmentStatus($shipment->uid));
    }

    #[DataProvider('notAllowedChangeDataProvider')]
    #[Test]
    public function changeDoesNothingForAChangeWhichIsNotAllowed(string $fromStatus, string $toStatus): void
    {
        $shipment = $this->addShipment($fromStatus, 1);
        $events = $this->collectEvents();

        $result = $this->subject->change($shipment, $toStatus, $fromStatus);

        self::assertSame(StatusChangeOutcome::NotAllowed, $result->shipmentStatusChange->outcome);
        self::assertSame($fromStatus, $this->fetchShipmentStatus($shipment->uid));
        self::assertSame([], $this->fetchOrderChanges());
        self::assertSame([], $events->getArrayCopy());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function notAllowedChangeDataProvider(): iterable
    {
        yield 'cancel a shipped shipment' => [ShipmentStatus::SHIPPED, ShipmentStatus::CANCELED];
        yield 'cancel a delivered shipment' => [ShipmentStatus::DELIVERED, ShipmentStatus::CANCELED];
        yield 'reopen a canceled shipment' => [ShipmentStatus::CANCELED, ShipmentStatus::OPEN];
        yield 'ship a canceled shipment' => [ShipmentStatus::CANCELED, ShipmentStatus::SHIPPED];
    }

    #[Test]
    public function changeDoesNothingIfTheStatusIsNotRegistered(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::OPEN, 1);

        $result = $this->subject->change($shipment, 'lost', ShipmentStatus::OPEN);

        self::assertSame(StatusChangeOutcome::UnknownStatus, $result->shipmentStatusChange->outcome);
        self::assertSame(ShipmentStatus::OPEN, $this->fetchShipmentStatus($shipment->uid));
    }

    #[Test]
    public function changeDoesNothingIfTheStatusIsNotChanged(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::OPEN, 1);

        $result = $this->subject->change($shipment, ShipmentStatus::OPEN, ShipmentStatus::OPEN);

        self::assertSame(StatusChangeOutcome::Unchanged, $result->shipmentStatusChange->outcome);
        self::assertSame([], $this->fetchOrderChanges());
    }

    #[Test]
    public function changeDoesNothingIfTheShipmentHasNotTheExpectedStatusAnymore(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::SHIPPED, 1);

        $result = $this->subject->change($shipment, ShipmentStatus::CANCELED, ShipmentStatus::OPEN);

        self::assertSame(StatusChangeOutcome::Stale, $result->shipmentStatusChange->outcome);
        self::assertSame(ShipmentStatus::SHIPPED, $result->shipmentStatusChange->storedStatus);
        self::assertSame(ShipmentStatus::SHIPPED, $this->fetchShipmentStatus($shipment->uid));
        self::assertSame([], $this->fetchOrderChanges());
    }

    #[Test]
    public function changeThrowsExceptionForAShipmentWhichIsNotStored(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1795600020);

        $this->subject->change(new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN), ShipmentStatus::SHIPPED, ShipmentStatus::OPEN);
    }

    #[Test]
    public function changeDispatchesTheBeforeAndTheAfterEventWithTheShipment(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::OPEN, 1);
        $events = $this->collectEvents();

        $this->subject->change($shipment, ShipmentStatus::IN_PROCESS, ShipmentStatus::OPEN, 'erp_sync', source: OrderChangeSource::Api);

        self::assertCount(2, $events);
        [$beforeEvent, $afterEvent] = $events->getArrayCopy();
        self::assertInstanceOf(BeforeShipmentStatusChangeEvent::class, $beforeEvent);
        self::assertSame($shipment, $beforeEvent->getShipmentWithStoredStatus());
        self::assertSame(ShipmentStatus::OPEN, $beforeEvent->getFromStatus());
        self::assertSame(ShipmentStatus::IN_PROCESS, $beforeEvent->getToStatus());
        self::assertSame(OrderChangeSource::Api, $beforeEvent->getSource());
        self::assertSame('erp_sync', $beforeEvent->getInitiator());
        self::assertSame(0, $beforeEvent->getBackendUserUid());
        self::assertInstanceOf(AfterShipmentStatusChangeEvent::class, $afterEvent);
        self::assertSame(ShipmentStatus::IN_PROCESS, $afterEvent->getShipmentWithNewStatus()->status, 'The shipment has the new status already.');
        self::assertSame($shipment->products, $afterEvent->getShipmentWithNewStatus()->products);
        self::assertSame(ShipmentStatus::OPEN, $afterEvent->getFromStatus());
        self::assertSame(ShipmentStatus::IN_PROCESS, $afterEvent->getToStatus());
        self::assertSame('shipment:' . $shipment->uid, $afterEvent->getOrderChange()->subject);
    }

    #[Test]
    public function aVetoOfABeforeEventListenerPreventsTheChange(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::IN_PROCESS, 1);
        $this->addEventListener(
            BeforeShipmentStatusChangeEvent::class,
            static function (BeforeShipmentStatusChangeEvent $event): void {
                $event->veto('The parcel is still in the warehouse.');
            }
        );

        $result = $this->subject->change($shipment, ShipmentStatus::SHIPPED, ShipmentStatus::IN_PROCESS);

        self::assertSame(StatusChangeOutcome::Vetoed, $result->shipmentStatusChange->outcome);
        self::assertSame('The parcel is still in the warehouse.', $result->shipmentStatusChange->vetoReason);
        self::assertSame(ShipmentStatus::IN_PROCESS, $this->fetchShipmentStatus($shipment->uid));
        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus(1));
        self::assertSame([], $this->fetchOrderChanges());
    }

    #[Test]
    public function errorsOfAfterEventListenersAreReturnedAndTheStatusIsChangedAnyway(): void
    {
        $shipment = $this->addShipment(ShipmentStatus::IN_PROCESS, 1);
        $this->addEventListener(
            AfterShipmentStatusChangeEvent::class,
            static function (AfterShipmentStatusChangeEvent $event): void {
                $event->addError('The shipping confirmation could not be sent.');
            }
        );

        $result = $this->subject->change($shipment, ShipmentStatus::SHIPPED, ShipmentStatus::IN_PROCESS);

        self::assertTrue($result->isChanged());
        self::assertSame(['The shipping confirmation could not be sent.'], $result->shipmentStatusChange->errors);
        self::assertSame(ShipmentStatus::SHIPPED, $this->fetchShipmentStatus($shipment->uid));
        self::assertSame(ShippingStatus::PARTIALLY_SHIPPED, $this->fetchShippingStatus(1));
    }

    private function addShipment(string $status, int $countOfFirstProduct): Shipment
    {
        $shipmentRepository = new ShipmentRepository($this->getConnectionPool(), GeneralUtility::makeInstance(Context::class));

        $shipmentUid = $shipmentRepository->add(new Shipment(
            orderItemUid: 1,
            status: $status,
            products: [new ShipmentProduct(orderProductUid: 11, count: $countOfFirstProduct)],
        ));
        $shipment = $shipmentRepository->findByUid($shipmentUid);
        self::assertInstanceOf(Shipment::class, $shipment);

        return $shipment;
    }

    /**
     * @return ArrayObject<int, object>
     */
    private function collectEvents(): ArrayObject
    {
        $events = new ArrayObject();
        $listener = static function (object $event) use ($events): void {
            $events->append($event);
        };
        $this->addEventListener(BeforeShipmentStatusChangeEvent::class, $listener, 'before');
        $this->addEventListener(AfterShipmentStatusChangeEvent::class, $listener, 'after');

        return $events;
    }

    private function addEventListener(string $eventClass, Closure $listener, string $name = 'listener'): void
    {
        $container = $this->getContainer();
        assert($container instanceof Container);

        $identifier = 'cart-test--event-listener--' . $name . '--' . md5($eventClass);
        $container->set($identifier, $listener);
        GeneralUtility::makeInstance(ListenerProvider::class)->addListener($eventClass, $identifier);
    }

    private function fetchShipmentStatus(int $shipmentUid): mixed
    {
        return $this->fetchStatus(self::ORDER_SHIPMENT_TABLE_NAME, $shipmentUid);
    }

    private function fetchShippingStatus(int $shippingUid): mixed
    {
        return $this->fetchStatus(self::ORDER_SHIPPING_TABLE_NAME, $shippingUid);
    }

    private function setShippingStatus(int $shippingUid, string $status): void
    {
        $this->getConnectionPool()->getConnectionForTable(self::ORDER_SHIPPING_TABLE_NAME)
            ->update(self::ORDER_SHIPPING_TABLE_NAME, ['status' => $status], ['uid' => $shippingUid])
        ;
    }

    private function fetchStatus(string $table, int $uid): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable($table)
            ->select(['status'], $table, ['uid' => $uid])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchOrderChanges(): array
    {
        return $this->getConnectionPool()->getConnectionForTable(self::ORDER_CHANGE_TABLE_NAME)
            ->select(
                ['item', 'type', 'subject', 'old_value', 'new_value', 'crdate', 'source', 'initiator'],
                self::ORDER_CHANGE_TABLE_NAME,
                [],
                [],
                ['uid' => 'ASC']
            )
            ->fetchAllAssociative()
        ;
    }
}
