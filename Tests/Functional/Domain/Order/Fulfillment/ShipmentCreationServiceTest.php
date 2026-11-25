<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\PhpDataSet;
use Codappix\Typo3PhpDatasets\TestingFramework;
use DateTimeImmutable;
use Extcode\Cart\Domain\Order\Fulfillment\OrderItemLock;
use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantityCalculator;
use Extcode\Cart\Domain\Order\Fulfillment\OrderShippingStatusUpdater;
use Extcode\Cart\Domain\Order\Fulfillment\Shipment;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentCreationOutcome;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentCreationResult;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentCreationService;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentProduct;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\History\OrderChangeType;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Domain\Order\Status\StatusChangeOutcome;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ShipmentCreationService::class)]
#[CoversClass(ShipmentCreationResult::class)]
#[CoversClass(OrderItemLock::class)]
#[CoversClass(OrderProductQuantityCalculator::class)]
#[CoversClass(OrderShippingStatusUpdater::class)]
final class ShipmentCreationServiceTest extends FunctionalTestCase
{
    use TestingFramework;

    private const NOW = '2026-11-25 10:30:00';

    private const ORDER_SHIPMENT_TABLE_NAME = 'tx_cart_domain_model_order_shipment';

    private const ORDER_SHIPMENT_PRODUCT_TABLE_NAME = 'tx_cart_domain_model_order_shipment_product';

    private const ORDER_SHIPPING_TABLE_NAME = 'tx_cart_domain_model_order_shipping';

    private const ORDER_CHANGE_TABLE_NAME = 'tx_cart_domain_model_order_change';

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private ShipmentCreationService $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderWithProducts.php');

        GeneralUtility::makeInstance(Context::class)->setAspect(
            'date',
            new DateTimeAspect(new DateTimeImmutable(self::NOW))
        );

        $subject = $this->get(ShipmentCreationService::class);
        self::assertInstanceOf(ShipmentCreationService::class, $subject);
        $this->subject = $subject;
    }

    #[Test]
    public function createStoresTheShipmentAndWritesItToTheOrderHistory(): void
    {
        $result = $this->subject->create(
            new Shipment(
                orderItemUid: 1,
                status: ShipmentStatus::IN_PROCESS,
                products: [
                    new ShipmentProduct(orderProductUid: 11, count: 2),
                    new ShipmentProduct(orderProductUid: 12, count: 1),
                ],
                carrier: 'DHL',
            ),
            'erp_sync',
            source: OrderChangeSource::Api
        );

        self::assertSame(ShipmentCreationOutcome::Created, $result->outcome);
        self::assertTrue($result->isCreated());
        self::assertNotNull($result->shipment);
        self::assertGreaterThan(0, $result->shipment->uid);
        self::assertSame('DHL', $result->shipment->carrier);
        self::assertSame(
            [[11, 2], [12, 1]],
            array_map(
                static fn (ShipmentProduct $shipmentProduct): array => [$shipmentProduct->orderProductUid, $shipmentProduct->count],
                $result->shipment->products
            )
        );
        self::assertSame(
            [
                [
                    'item' => 1,
                    'type' => OrderChangeType::SHIPMENT_STATUS,
                    'subject' => 'shipment:' . $result->shipment->uid,
                    'old_value' => '',
                    'new_value' => ShipmentStatus::IN_PROCESS,
                    'data' => '{"products":[{"orderProduct":11,"count":2},{"orderProduct":12,"count":1}]}',
                    'crdate' => (new DateTimeImmutable(self::NOW))->getTimestamp(),
                    'source' => 'api',
                    'initiator' => 'erp_sync',
                ],
            ],
            $this->fetchOrderChanges()
        );
        self::assertNotNull($result->orderChange);
        self::assertSame($this->fetchOrderChangeUids(), [$result->orderChange->uid]);
    }

    #[Test]
    public function createDoesNotChangeTheShippingStatusOfTheOrderAsLongAsNothingIsShipped(): void
    {
        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [new ShipmentProduct(orderProductUid: 11, count: 3), new ShipmentProduct(orderProductUid: 12, count: 1)],
        ));

        self::assertNull($result->shippingStatusChange);
        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus(1));
    }

    #[Test]
    public function createChangesTheShippingStatusOfTheOrderToPartiallyShipped(): void
    {
        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            products: [new ShipmentProduct(orderProductUid: 11, count: 1)],
        ));

        self::assertSame(StatusChangeOutcome::Changed, $result->shippingStatusChange?->outcome);
        self::assertSame(ShippingStatus::PARTIALLY_SHIPPED, $this->fetchShippingStatus(1));
        self::assertSame(
            [
                [OrderChangeType::SHIPMENT_STATUS, '', ShipmentStatus::SHIPPED],
                [OrderChangeType::SHIPPING_STATUS, ShippingStatus::OPEN, ShippingStatus::PARTIALLY_SHIPPED],
            ],
            array_map(
                static fn (array $orderChange): array => [$orderChange['type'], $orderChange['old_value'], $orderChange['new_value']],
                $this->fetchOrderChanges()
            )
        );
    }

    #[Test]
    public function createChangesTheShippingStatusOfTheOrderToShippedIfAllShippableProductsAreShipped(): void
    {
        $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            products: [new ShipmentProduct(orderProductUid: 11, count: 2)],
        ));
        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::DELIVERED,
            products: [new ShipmentProduct(orderProductUid: 11, count: 1), new ShipmentProduct(orderProductUid: 12, count: 1)],
        ));

        self::assertSame(StatusChangeOutcome::Changed, $result->shippingStatusChange?->outcome);
        self::assertSame(ShippingStatus::SHIPPED, $this->fetchShippingStatus(1));
    }

    #[Test]
    public function createStoresNothingIfACountExceedsTheCountAvailableForShipment(): void
    {
        $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [new ShipmentProduct(orderProductUid: 11, count: 2)],
        ));
        $orderChangeUids = $this->fetchOrderChangeUids();

        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            products: [new ShipmentProduct(orderProductUid: 11, count: 2), new ShipmentProduct(orderProductUid: 12, count: 1)],
        ));

        self::assertSame(ShipmentCreationOutcome::QuantityExceeded, $result->outcome);
        self::assertFalse($result->isCreated());
        self::assertSame([11], $result->exceededOrderProductUids);
        self::assertNull($result->shipment);
        self::assertCount(1, $this->fetchRows(self::ORDER_SHIPMENT_TABLE_NAME));
        self::assertCount(1, $this->fetchRows(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME));
        self::assertSame($orderChangeUids, $this->fetchOrderChangeUids());
        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus(1));
    }

    #[Test]
    public function createSumsUpTheCountsOfAProductGivenTwice(): void
    {
        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [new ShipmentProduct(orderProductUid: 12, count: 1), new ShipmentProduct(orderProductUid: 12, count: 1)],
        ));

        self::assertSame([12], $result->exceededOrderProductUids);
    }

    #[Test]
    public function theCountOfACanceledShipmentIsAvailableForShipmentAgain(): void
    {
        (new PhpDataSet())->import([
            self::ORDER_SHIPMENT_TABLE_NAME => [
                ['uid' => 1, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::CANCELED, 'products' => 1],
            ],
            self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME => [
                ['uid' => 1, 'pid' => 105, 'shipment' => 1, 'order_product' => 11, 'count' => 3],
            ],
        ]);

        $result = $this->subject->create(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [new ShipmentProduct(orderProductUid: 11, count: 3)],
        ));

        self::assertTrue($result->isCreated());
    }

    #[DataProvider('invalidShipmentDataProvider')]
    #[Test]
    public function createThrowsExceptionForAnInvalidShipment(Shipment $shipment, int $expectedExceptionCode): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode($expectedExceptionCode);

        try {
            $this->subject->create($shipment);
        } finally {
            self::assertSame([], $this->fetchRows(self::ORDER_SHIPMENT_TABLE_NAME));
            self::assertSame([], $this->fetchOrderChangeUids());
        }
    }

    /**
     * @return iterable<string, array{Shipment, int}>
     */
    public static function invalidShipmentDataProvider(): iterable
    {
        $products = [new ShipmentProduct(orderProductUid: 11, count: 1)];

        yield 'stored already' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN, products: $products, uid: 5),
            1795600010,
        ];
        yield 'canceled' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::CANCELED, products: $products),
            1795600011,
        ];
        yield 'unknown status' => [
            new Shipment(orderItemUid: 1, status: 'lost', products: $products),
            1795600011,
        ];
        yield 'without products' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN),
            1795600012,
        ];
        yield 'count 0' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN, products: [new ShipmentProduct(orderProductUid: 11, count: 0)]),
            1795600014,
        ];
        yield 'product of another order' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN, products: [new ShipmentProduct(orderProductUid: 21, count: 1)]),
            1795600015,
        ];
        yield 'product which is not shippable' => [
            new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN, products: [new ShipmentProduct(orderProductUid: 13, count: 1)]),
            1795600016,
        ];
        yield 'order which does not exist' => [
            new Shipment(orderItemUid: 99, status: ShipmentStatus::OPEN, products: $products),
            1795600002,
        ];
    }

    private function fetchShippingStatus(int $shippingUid): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable(self::ORDER_SHIPPING_TABLE_NAME)
            ->select(['status'], self::ORDER_SHIPPING_TABLE_NAME, ['uid' => $shippingUid])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }

    /**
     * @return list<int>
     */
    private function fetchOrderChangeUids(): array
    {
        return array_map(
            static fn (array $row): int => is_numeric($row['uid']) ? (int) $row['uid'] : 0,
            $this->fetchRows(self::ORDER_CHANGE_TABLE_NAME, ['uid'])
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchOrderChanges(): array
    {
        return $this->fetchRows(
            self::ORDER_CHANGE_TABLE_NAME,
            ['item', 'type', 'subject', 'old_value', 'new_value', 'data', 'crdate', 'source', 'initiator']
        );
    }

    /**
     * @param list<string> $columns
     *
     * @return list<array<string, mixed>>
     */
    private function fetchRows(string $table, array $columns = ['*']): array
    {
        return $this->getConnectionPool()->getConnectionForTable($table)
            ->select($columns, $table, [], [], ['uid' => 'ASC'])
            ->fetchAllAssociative()
        ;
    }
}
