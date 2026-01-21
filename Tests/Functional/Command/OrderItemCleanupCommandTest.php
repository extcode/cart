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
use Extcode\Cart\Service\OrderItemCleanupService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(OrderItemCleanupCommand::class)]
#[CoversClass(OrderItemCleanupService::class)]
final class OrderItemCleanupCommandTest extends AbstractCommandTestCase
{
    #[Test]
    public function doesNotTouchRecordsCreatedAfterCutOffDate(): void
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
    public function doesTouchNoneMatchingRecords(): void
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
}
