<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Command;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\PhpDataSet;
use DateTimeImmutable;
use Extcode\Cart\Command\OrderItemCleanupCommand;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Service\OrderItemCleanupService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(OrderItemCleanupCommand::class)]
#[CoversClass(OrderItemCleanupService::class)]
final class OrderItemCleanupCommandTest extends AbstractCommandTestCase
{
    #[Test]
    public function doesNotDeletesRecordsCreatedAfterCutOffDate(): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                [
                    'crdate' => (int) (new DateTimeImmutable('2026-01-21'))->format('U'),
                ],
                [
                    'crdate' => (int) (new DateTimeImmutable('2025-01-21'))->format('U'),
                ],
                [
                    'crdate' => (int) (new DateTimeImmutable('2025-01-02'))->format('U'),
                ],
                [
                    'crdate' => (int) (new DateTimeImmutable('2025-01-01'))->format('U'),
                ],
            ],
        ]);

        $commandTester = new CommandTester($this->get(OrderItemCleanupCommand::class));
        $commandTester->execute(
            [
                'cutOffDate' => '01-01-2025'
            ]
        );

        $records = $this->getAllRecords('tx_cart_domain_model_order_item');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        self::assertSame(0, $records[2]['deleted']);
        self::assertSame(0, $records[3]['deleted']);
    }

    #[Test]
    public function deletesRecordsCreatedBeforeCutOffDate(): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                [
                    'crdate' => (int) (new DateTimeImmutable('2024-12-31'))->format('U'),
                ],
                [
                    'crdate' => (int) (new DateTimeImmutable('2024-10-12'))->format('U'),
                ],
            ],
        ]);

        $commandTester = new CommandTester($this->get(OrderItemCleanupCommand::class));
        $commandTester->execute(
            [
                'cutOffDate' => '01-01-2025'
            ]
        );

        $records = $this->getAllRecords('tx_cart_domain_model_order_item');
        self::assertSame(1, $records[0]['deleted']);
        self::assertSame(1, $records[1]['deleted']);
    }

    #[Test]
    public function deletesRelatedRecordsCreatedBeforeCutOffDate(): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                [
                    'uid' => 10,
                    'products' => 2,
                    'billing_address' => 1,
                    'shipping_address' => 1,
                    'payment' => 1,
                    'shipping' => 1,
                    'tax_class' => 3,
                    'crdate' => (int) (new DateTimeImmutable('2024-12-31'))->format('U'),
                ],
            ],
            'tx_cart_domain_model_order_product' => [
                [
                    'uid' => 1,
                    'item' => 10,
                ],
                [
                    'uid' => 2,
                    'item' => 10,
                ],
            ],
            'tx_cart_domain_model_order_address' => [
                [
                    'uid' => 100,
                    'item' => 10,
                    'record_type' => '\\' . BillingAddress::class,
                ],
                [
                    'uid' => 101,
                    'item' => 10,
                    'record_type' => '\\' . ShippingAddress::class,
                ],
            ],
            'tx_cart_domain_model_order_payment' => [
                [
                    'uid' => 30,
                    'item' => 10,
                ],
            ],
            'tx_cart_domain_model_order_shipping' => [
                [
                    'uid' => 30,
                    'item' => 10,
                ],
            ],
            'tx_cart_domain_model_order_taxclass' => [
                [
                    'uid' => 30,
                    'item' => 10,
                ],
                [
                    'uid' => 31,
                    'item' => 10,
                ],
                [
                    'uid' => 32,
                    'item' => 10,
                ],
            ],
        ]);

        $commandTester = new CommandTester($this->get(OrderItemCleanupCommand::class));
        $commandTester->execute(
            [
                'cutOffDate' => '01-01-2025'
            ]
        );

        $records = $this->getAllRecords('tx_cart_domain_model_order_item');
        self::assertSame(1, $records[0]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_product');
        self::assertSame(1, $records[0]['deleted']);
        self::assertSame(1, $records[1]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_address');
        self::assertSame(1, $records[0]['deleted']);
        self::assertSame(1, $records[1]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_payment');
        self::assertSame(1, $records[0]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_shipping');
        self::assertSame(1, $records[0]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_taxclass');
        self::assertSame(1, $records[0]['deleted']);
        self::assertSame(1, $records[1]['deleted']);
        self::assertSame(1, $records[2]['deleted']);
    }



    #[Test]
    public function doesNotDeletesNotRelatedRecordsCreatedBeforeCutOffDate(): void
    {
        (new PhpDataSet())->import([
            'tx_cart_domain_model_order_item' => [
                [
                    'uid' => 10,
                    'products' => 2,
                    'billing_address' => 1,
                    'shipping_address' => 1,
                    'payment' => 1,
                    'shipping' => 1,
                    'tax_class' => 3,
                    'crdate' => (int) (new DateTimeImmutable('2024-12-31'))->format('U'),
                ],
            ],
            'tx_cart_domain_model_order_product' => [
                [
                    'uid' => 1,
                    'item' => 9,
                ],
                [
                    'uid' => 2,
                    'item' => 11,
                ],
            ],
            'tx_cart_domain_model_order_address' => [
                [
                    'item' => 9,
                    'record_type' => '\\' . BillingAddress::class,
                ],
                [
                    'item' => 9,
                    'record_type' => '\\' . ShippingAddress::class,
                ],
                [
                    'item' => 11,
                    'record_type' => '\\' . BillingAddress::class,
                ],
                [
                    'item' => 11,
                    'record_type' => '\\' . ShippingAddress::class,
                ],
            ],
            'tx_cart_domain_model_order_payment' => [
                [
                    'item' => 9,
                ],
                [
                    'item' => 11,
                ],
            ],
            'tx_cart_domain_model_order_shipping' => [
                [
                    'item' => 9,
                ],
                [
                    'item' => 11,
                ],
            ],
            'tx_cart_domain_model_order_taxclass' => [
                [
                    'item' => 9,
                ],
                [
                    'item' => 9,
                ],
                [
                    'item' => 9,
                ],
                [
                    'item' => 11,
                ],
                [
                    'item' => 11,
                ],
                [
                    'item' => 11,
                ],
            ],
        ]);

        $commandTester = new CommandTester($this->get(OrderItemCleanupCommand::class));
        $commandTester->execute(
            [
                'cutOffDate' => '01-01-2025'
            ]
        );

        $records = $this->getAllRecords('tx_cart_domain_model_order_item');
        self::assertSame(1, $records[0]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_product');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_address');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        self::assertSame(0, $records[2]['deleted']);
        self::assertSame(0, $records[3]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_payment');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_shipping');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        $records = $this->getAllRecords('tx_cart_domain_model_order_taxclass');
        self::assertSame(0, $records[0]['deleted']);
        self::assertSame(0, $records[1]['deleted']);
        self::assertSame(0, $records[2]['deleted']);
        self::assertSame(0, $records[3]['deleted']);
        self::assertSame(0, $records[4]['deleted']);
        self::assertSame(0, $records[5]['deleted']);
    }
}
