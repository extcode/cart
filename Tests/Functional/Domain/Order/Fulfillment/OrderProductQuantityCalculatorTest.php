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
use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantities;
use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantity;
use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantityCalculator;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(OrderProductQuantityCalculator::class)]
#[CoversClass(OrderProductQuantity::class)]
#[CoversClass(OrderProductQuantities::class)]
final class OrderProductQuantityCalculatorTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private OrderProductQuantityCalculator $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderWithProducts.php');

        $subject = $this->get(OrderProductQuantityCalculator::class);
        self::assertInstanceOf(OrderProductQuantityCalculator::class, $subject);
        $this->subject = $subject;
    }

    #[Test]
    public function calculateForOrderItemReturnsTheQuantitiesOfTheOrderedProductsFromTheShipments(): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_shipment' => [
                ['uid' => 1, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::SHIPPED],
                ['uid' => 2, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::DELIVERED],
                ['uid' => 3, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::IN_PROCESS],
                ['uid' => 4, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::CANCELED],
                ['uid' => 5, 'pid' => 105, 'item' => 1, 'status' => ShipmentStatus::SHIPPED, 'deleted' => 1],
                ['uid' => 6, 'pid' => 105, 'item' => 2, 'status' => ShipmentStatus::SHIPPED],
            ],
            'tx_cart_domain_model_order_shipment_product' => [
                ['uid' => 1, 'pid' => 105, 'shipment' => 1, 'order_product' => 11, 'count' => 1],
                ['uid' => 2, 'pid' => 105, 'shipment' => 2, 'order_product' => 11, 'count' => 1],
                ['uid' => 3, 'pid' => 105, 'shipment' => 3, 'order_product' => 12, 'count' => 1],
                ['uid' => 4, 'pid' => 105, 'shipment' => 4, 'order_product' => 11, 'count' => 1],
                ['uid' => 5, 'pid' => 105, 'shipment' => 5, 'order_product' => 11, 'count' => 1],
                ['uid' => 6, 'pid' => 105, 'shipment' => 6, 'order_product' => 21, 'count' => 1],
            ],
        ]);

        $orderProductQuantities = iterator_to_array($this->subject->calculateForOrderItem(1));

        self::assertSame([11, 12, 13], array_keys($orderProductQuantities));
        self::assertSame(
            [
                11 => [true, 3, 2, 2, 1],
                12 => [true, 1, 1, 0, 0],
                13 => [false, 1, 0, 0, 0],
            ],
            array_map(
                static fn (OrderProductQuantity $orderProductQuantity): array => [
                    $orderProductQuantity->isShippable,
                    $orderProductQuantity->orderedCount,
                    $orderProductQuantity->countInUncanceledShipments,
                    $orderProductQuantity->shippedCount,
                    $orderProductQuantity->getCountAvailableForShipment(),
                ],
                $orderProductQuantities
            )
        );
    }

    #[Test]
    public function calculateForOrderItemReturnsNothingForAnOrderWithoutProducts(): void
    {
        self::assertCount(0, $this->subject->calculateForOrderItem(99));
    }
}
