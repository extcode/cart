<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantities;
use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantity;
use Extcode\Cart\Domain\Order\Fulfillment\ShippingStatusDerivation;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ShippingStatusDerivation::class)]
#[CoversClass(OrderProductQuantity::class)]
#[CoversClass(OrderProductQuantities::class)]
final class ShippingStatusDerivationTest extends UnitTestCase
{
    /**
     * @param list<OrderProductQuantity> $orderProductQuantities
     */
    #[DataProvider('derivationDataProvider')]
    #[Test]
    public function deriveReturnsTheStatusDerivedFromTheOrderProductQuantities(string $storedStatus, array $orderProductQuantities, string $expectedStatus): void
    {
        self::assertSame($expectedStatus, ShippingStatusDerivation::derive($storedStatus, new OrderProductQuantities(...$orderProductQuantities)));
    }

    /**
     * @return iterable<string, array{string, list<OrderProductQuantity>, string}>
     */
    public static function derivationDataProvider(): iterable
    {
        yield 'no products' => [
            ShippingStatus::OPEN,
            [],
            ShippingStatus::NOT_REQUIRED,
        ];
        yield 'no shippable product' => [
            ShippingStatus::OPEN,
            [self::orderProductQuantity(1, 1, isShippable: false)],
            ShippingStatus::NOT_REQUIRED,
        ];
        foreach ([ShippingStatus::OPEN, ShippingStatus::ON_HOLD, ShippingStatus::IN_PROCESS] as $manualStatus) {
            yield 'nothing shipped keeps ' . $manualStatus => [
                $manualStatus,
                [self::orderProductQuantity(2, 2, countInUncanceledShipments: 2)],
                $manualStatus,
            ];
        }
        yield 'nothing shipped anymore falls back from partially shipped to in process' => [
            ShippingStatus::PARTIALLY_SHIPPED,
            [self::orderProductQuantity(3, 2)],
            ShippingStatus::IN_PROCESS,
        ];
        yield 'nothing shipped anymore falls back from shipped to in process' => [
            ShippingStatus::SHIPPED,
            [self::orderProductQuantity(4, 2, countInUncanceledShipments: 2)],
            ShippingStatus::IN_PROCESS,
        ];
        yield 'nothing shipped keeps not required of an order with shippable products' => [
            ShippingStatus::NOT_REQUIRED,
            [self::orderProductQuantity(5, 2)],
            ShippingStatus::NOT_REQUIRED,
        ];
        yield 'some count of a product shipped' => [
            ShippingStatus::IN_PROCESS,
            [self::orderProductQuantity(6, 3, countInUncanceledShipments: 1, shippedCount: 1)],
            ShippingStatus::PARTIALLY_SHIPPED,
        ];
        yield 'one of two products shipped' => [
            ShippingStatus::OPEN,
            [self::orderProductQuantity(7, 1, countInUncanceledShipments: 1, shippedCount: 1), self::orderProductQuantity(8, 1)],
            ShippingStatus::PARTIALLY_SHIPPED,
        ];
        yield 'everything shipped' => [
            ShippingStatus::PARTIALLY_SHIPPED,
            [self::orderProductQuantity(9, 2, countInUncanceledShipments: 2, shippedCount: 2), self::orderProductQuantity(10, 1, countInUncanceledShipments: 1, shippedCount: 1)],
            ShippingStatus::SHIPPED,
        ];
        yield 'everything shippable shipped, a product which is not shippable does not count' => [
            ShippingStatus::OPEN,
            [self::orderProductQuantity(11, 1, countInUncanceledShipments: 1, shippedCount: 1), self::orderProductQuantity(12, 1, isShippable: false)],
            ShippingStatus::SHIPPED,
        ];
        yield 'a replacement exceeds the ordered count' => [
            ShippingStatus::SHIPPED,
            [self::orderProductQuantity(13, 1, countInUncanceledShipments: 2, shippedCount: 2)],
            ShippingStatus::SHIPPED,
        ];
    }

    #[Test]
    public function theCountAvailableForShipmentIsTheOrderedCountWithoutTheCountInUncanceledShipments(): void
    {
        self::assertSame(2, self::orderProductQuantity(14, 3, countInUncanceledShipments: 1)->getCountAvailableForShipment());
        self::assertSame(0, self::orderProductQuantity(15, 1, countInUncanceledShipments: 2)->getCountAvailableForShipment());
        self::assertSame(0, self::orderProductQuantity(16, 3, isShippable: false)->getCountAvailableForShipment());
    }

    private static function orderProductQuantity(
        int $orderProductUid,
        int $orderedCount,
        bool $isShippable = true,
        int $countInUncanceledShipments = 0,
        int $shippedCount = 0
    ): OrderProductQuantity {
        return new OrderProductQuantity($orderProductUid, $isShippable, $orderedCount, $countInUncanceledShipments, $shippedCount);
    }
}
