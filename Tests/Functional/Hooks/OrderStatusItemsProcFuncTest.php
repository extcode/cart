<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Hooks\OrderStatusItemsProcFunc;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The status field of the payment and the shipping in the TCA (e.g. the list
 * module) contains the registered status of cart and of other extensions,
 * see Tests/Fixtures/cart_order_status/Configuration/OrderStatus.php.
 */
#[CoversClass(OrderStatusItemsProcFunc::class)]
final class OrderStatusItemsProcFuncTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status',
    ];

    /**
     * @param list<array{label: string, value: string}> $expectedItems
     */
    #[DataProvider('tableDataProvider')]
    #[Test]
    public function itemsProcFuncOfTheStatusFieldAddsTheRegisteredStatusInTheirOrder(string $table, array $expectedItems): void
    {
        $tca = $GLOBALS['TCA'];
        self::assertIsArray($tca);
        $config = ArrayUtility::getValueByPath($tca, [$table, 'columns', 'status', 'config']);
        self::assertIsArray($config);
        self::assertSame([], $config['items'] ?? null, 'The items must only be added by the itemsProcFunc.');
        $itemsProcFunc = $config['itemsProcFunc'] ?? null;
        self::assertIsString($itemsProcFunc);
        self::assertNotSame('', $itemsProcFunc);

        $parameters = [
            'items' => [],
            'config' => $config,
            'table' => $table,
            'field' => 'status',
        ];

        GeneralUtility::callUserFunction($itemsProcFunc, $parameters, $this);

        self::assertIsArray($parameters);
        self::assertSame($expectedItems, $parameters['items'] ?? null);
    }

    /**
     * @return iterable<string, array{table: string, expectedItems: list<array{label: string, value: string}>}>
     */
    public static function tableDataProvider(): iterable
    {
        $cartLLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';
        $ownLLL = 'LLL:EXT:cart_order_status/Resources/Private/Language/locallang.xlf:';

        yield 'payment' => [
            'table' => 'tx_cart_domain_model_order_payment',
            'expectedItems' => [
                ['label' => $ownLLL . 'payment.status.new', 'value' => 'new'],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_payment.status.open', 'value' => PaymentStatus::OPEN],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_payment.status.pending', 'value' => PaymentStatus::PENDING],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_payment.status.paid', 'value' => PaymentStatus::PAID],
                ['label' => $ownLLL . 'payment.status.refunded', 'value' => 'refunded'],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_payment.status.canceled', 'value' => PaymentStatus::CANCELED],
                ['label' => $ownLLL . 'payment.status.chargeback', 'value' => 'chargeback'],
            ],
        ];
        yield 'shipping' => [
            'table' => 'tx_cart_domain_model_order_shipping',
            'expectedItems' => [
                ['label' => $ownLLL . 'shipping.status.ordered', 'value' => 'ordered'],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_shipping.status.open', 'value' => ShippingStatus::OPEN],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_shipping.status.on_hold', 'value' => ShippingStatus::ON_HOLD],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_shipping.status.in_process', 'value' => ShippingStatus::IN_PROCESS],
                ['label' => $ownLLL . 'shipping.status.packed', 'value' => 'packed'],
                ['label' => $cartLLL . 'tx_cart_domain_model_order_shipping.status.shipped', 'value' => ShippingStatus::SHIPPED],
                ['label' => $ownLLL . 'shipping.status.delivered', 'value' => 'delivered'],
            ],
        ];
    }
}
