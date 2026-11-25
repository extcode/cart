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
use Extcode\Cart\Domain\Order\Fulfillment\Shipment;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentProduct;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentRepository;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ShipmentRepository::class)]
final class ShipmentRepositoryTest extends FunctionalTestCase
{
    use TestingFramework;

    private const CURRENT_DATE = '2026-11-24 10:30:00';

    private const ORDER_SHIPMENT_TABLE_NAME = 'tx_cart_domain_model_order_shipment';

    private const ORDER_SHIPMENT_PRODUCT_TABLE_NAME = 'tx_cart_domain_model_order_shipment_product';

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private ShipmentRepository $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                ['uid' => 1, 'pid' => 105, 'order_number' => 'O-1', 'products' => 2],
                ['uid' => 2, 'pid' => 105, 'order_number' => 'O-2', 'products' => 1],
            ],
            'tx_cart_domain_model_order_product' => [
                ['uid' => 11, 'pid' => 105, 'item' => 1, 'sku' => 'book', 'count' => 3],
                ['uid' => 12, 'pid' => 105, 'item' => 1, 'sku' => 'mug', 'count' => 1],
                ['uid' => 21, 'pid' => 105, 'item' => 2, 'sku' => 'book', 'count' => 1],
            ],
        ]);

        $context = GeneralUtility::makeInstance(Context::class);
        $context->setAspect('date', new DateTimeAspect(new DateTimeImmutable(self::CURRENT_DATE)));

        $this->subject = new ShipmentRepository($this->getConnectionPool(), $context);
    }

    #[Test]
    public function addStoresTheShipmentWithItsProductsOnThePageOfTheOrder(): void
    {
        $shipmentUid = $this->subject->add(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            products: [
                new ShipmentProduct(orderProductUid: 11, count: 2),
                new ShipmentProduct(orderProductUid: 12, count: 1),
            ],
            carrier: 'DHL',
            trackingNumber: '00340434161094042557',
            trackingUrl: 'https://example.org/tracking/00340434161094042557',
            shippedDate: new DateTimeImmutable('2026-11-23 15:00:00'),
            note: 'first parcel',
        ));

        $currentTimestamp = (new DateTimeImmutable(self::CURRENT_DATE))->getTimestamp();
        self::assertSame(
            [
                [
                    'uid' => $shipmentUid,
                    'pid' => 105,
                    'tstamp' => $currentTimestamp,
                    'crdate' => $currentTimestamp,
                    'item' => 1,
                    'status' => ShipmentStatus::SHIPPED,
                    'replacement' => 0,
                    'carrier' => 'DHL',
                    'tracking_number' => '00340434161094042557',
                    'tracking_url' => 'https://example.org/tracking/00340434161094042557',
                    'shipped_date' => (new DateTimeImmutable('2026-11-23 15:00:00'))->getTimestamp(),
                    'note' => 'first parcel',
                    'products' => 2,
                ],
            ],
            $this->fetchRows(
                self::ORDER_SHIPMENT_TABLE_NAME,
                ['uid', 'pid', 'tstamp', 'crdate', 'item', 'status', 'replacement', 'carrier', 'tracking_number', 'tracking_url', 'shipped_date', 'note', 'products']
            )
        );
        self::assertSame(
            [
                ['pid' => 105, 'shipment' => $shipmentUid, 'order_product' => 11, 'count' => 2],
                ['pid' => 105, 'shipment' => $shipmentUid, 'order_product' => 12, 'count' => 1],
            ],
            $this->fetchRows(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME, ['pid', 'shipment', 'order_product', 'count'])
        );
    }

    #[Test]
    public function addCountsTheShipmentsOfTheOrderItem(): void
    {
        $this->subject->add(new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN));
        $this->subject->add(new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN));

        self::assertSame(
            [
                ['uid' => 1, 'shipments' => 2],
                ['uid' => 2, 'shipments' => 0],
            ],
            $this->fetchRows('tx_cart_domain_model_order_item', ['uid', 'shipments'])
        );
    }

    #[Test]
    public function addThrowsAnExceptionForAnUnknownOrderItem(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('The order item 3 does not exist.', 1795347001));

        $this->subject->add(new Shipment(orderItemUid: 3, status: ShipmentStatus::OPEN));
    }

    #[Test]
    public function findByUidReturnsTheStoredShipmentWithItsProducts(): void
    {
        $shipment = new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::IN_PROCESS,
            products: [
                new ShipmentProduct(orderProductUid: 11, count: 1),
            ],
            carrier: 'DPD',
            note: 'replacement for a damaged book',
            isReplacement: true,
        );
        $shipmentUid = $this->subject->add($shipment);

        $foundShipment = $this->subject->findByUid($shipmentUid);

        self::assertInstanceOf(Shipment::class, $foundShipment);
        self::assertSame($shipmentUid, $foundShipment->uid);
        self::assertSame(1, $foundShipment->orderItemUid);
        self::assertSame(ShipmentStatus::IN_PROCESS, $foundShipment->status);
        self::assertSame('DPD', $foundShipment->carrier);
        self::assertSame('', $foundShipment->trackingNumber);
        self::assertSame('', $foundShipment->trackingUrl);
        self::assertNull($foundShipment->shippedDate);
        self::assertSame('replacement for a damaged book', $foundShipment->note);
        self::assertTrue($foundShipment->isReplacement);
        self::assertCount(1, $foundShipment->products);
        self::assertSame(11, $foundShipment->products[0]->orderProductUid);
        self::assertSame(1, $foundShipment->products[0]->count);
        self::assertGreaterThan(0, $foundShipment->products[0]->uid);
    }

    #[Test]
    public function findByUidReturnsTheShippedDate(): void
    {
        $shipmentUid = $this->subject->add(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            shippedDate: new DateTimeImmutable('2026-11-23 15:00:00'),
        ));

        self::assertSame(
            (new DateTimeImmutable('2026-11-23 15:00:00'))->getTimestamp(),
            $this->subject->findByUid($shipmentUid)?->shippedDate?->getTimestamp()
        );
    }

    #[Test]
    public function findByUidReturnsNullForAnUnknownOrDeletedShipment(): void
    {
        $shipmentUid = $this->subject->add(new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN));
        $this->getConnectionPool()
            ->getConnectionForTable(self::ORDER_SHIPMENT_TABLE_NAME)
            ->update(self::ORDER_SHIPMENT_TABLE_NAME, ['deleted' => 1], ['uid' => $shipmentUid])
        ;

        self::assertNull($this->subject->findByUid($shipmentUid));
        self::assertNull($this->subject->findByUid($shipmentUid + 1));
    }

    #[Test]
    public function findByOrderItemReturnsTheShipmentsOfTheOrderInTheOrderTheyWereCreated(): void
    {
        $firstShipmentUid = $this->subject->add(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::SHIPPED,
            products: [new ShipmentProduct(orderProductUid: 11, count: 2)],
        ));
        $this->subject->add(new Shipment(
            orderItemUid: 2,
            status: ShipmentStatus::SHIPPED,
            products: [new ShipmentProduct(orderProductUid: 21, count: 1)],
        ));
        $secondShipmentUid = $this->subject->add(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [
                new ShipmentProduct(orderProductUid: 11, count: 1),
                new ShipmentProduct(orderProductUid: 12, count: 1),
            ],
        ));

        $shipments = $this->subject->findByOrderItem(1);

        self::assertSame([$firstShipmentUid, $secondShipmentUid], array_map(static fn (Shipment $shipment): int => $shipment->uid, $shipments));
        self::assertSame(
            [[11 => 2], [11 => 1, 12 => 1]],
            array_map(
                static function (Shipment $shipment): array {
                    $countsByOrderProductUid = [];
                    foreach ($shipment->products as $shipmentProduct) {
                        $countsByOrderProductUid[$shipmentProduct->orderProductUid] = $shipmentProduct->count;
                    }

                    return $countsByOrderProductUid;
                },
                $shipments
            )
        );
    }

    #[Test]
    public function findByOrderItemSkipsDeletedShipmentsAndShipmentProducts(): void
    {
        $deletedShipmentUid = $this->subject->add(new Shipment(orderItemUid: 1, status: ShipmentStatus::OPEN));
        $keptShipmentUid = $this->subject->add(new Shipment(
            orderItemUid: 1,
            status: ShipmentStatus::OPEN,
            products: [
                new ShipmentProduct(orderProductUid: 11, count: 1),
                new ShipmentProduct(orderProductUid: 12, count: 1),
            ],
        ));
        $connectionPool = $this->getConnectionPool();
        $connectionPool->getConnectionForTable(self::ORDER_SHIPMENT_TABLE_NAME)
            ->update(self::ORDER_SHIPMENT_TABLE_NAME, ['deleted' => 1], ['uid' => $deletedShipmentUid])
        ;
        $connectionPool->getConnectionForTable(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME)
            ->update(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME, ['deleted' => 1], ['order_product' => 12])
        ;

        $shipments = $this->subject->findByOrderItem(1);

        self::assertCount(1, $shipments);
        self::assertSame($keptShipmentUid, $shipments[0]->uid);
        self::assertCount(1, $shipments[0]->products);
        self::assertSame(11, $shipments[0]->products[0]->orderProductUid);
    }

    #[Test]
    public function findByOrderItemReturnsAnEmptyListForAnOrderWithoutShipments(): void
    {
        self::assertSame([], $this->subject->findByOrderItem(2));
    }

    /**
     * @param list<string> $fields
     *
     * @return list<array<string, mixed>>
     */
    private function fetchRows(string $table, array $fields): array
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->select(...$fields)
            ->from($table)
            ->orderBy('uid')
            ->executeQuery()
            ->fetchAllAssociative()
        ;
    }
}
