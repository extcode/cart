<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractWidgetTestCase extends FunctionalTestCase
{
    use TestingFramework;

    /**
     * Page uid of the sysfolder holding the orders, see Tests/Fixtures/BaseDatabase.php
     */
    protected const ORDER_STORAGE_PID = 105;

    protected const FILTER_PAYMENT_PAID_SHIPPING_OPEN = [
        'filter' => [
            'payment' => [
                'status' => 'paid',
            ],
            'shipping' => [
                'status' => 'open',
            ],
        ],
    ];

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-dashboard',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../Fixtures/BackendUser.php');

        $this->setUpBackendUser(1);

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('en');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['LANG']
        );

        parent::tearDown();
    }

    /**
     * Creates an order with one payment and one shipping.
     *
     * @param string $orderDate relative date for strtotime(), e.g. "-1 day 12:00"
     */
    protected function createOrder(
        int $uid,
        string $orderDate,
        string $paymentStatus = 'paid',
        string $shippingStatus = 'open',
        float $totalGross = 0.0,
    ): void {
        $connectionPool = $this->getConnectionPool();

        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_item')->insert(
            'tx_cart_domain_model_order_item',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'crdate' => strtotime($orderDate),
                'order_number' => 'O-' . $uid,
                'order_date' => strtotime($orderDate),
                'total_gross' => $totalGross,
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_payment')->insert(
            'tx_cart_domain_model_order_payment',
            [
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'name' => 'Payment ' . $uid,
                'status' => $paymentStatus,
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_shipping')->insert(
            'tx_cart_domain_model_order_shipping',
            [
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'name' => 'Shipping ' . $uid,
                'status' => $shippingStatus,
            ]
        );
    }

    /**
     * @param array<mixed> $chartData
     *
     * @return array<mixed>
     */
    protected static function getData(array $chartData): array
    {
        self::assertIsArray($chartData['datasets']);
        self::assertIsArray($chartData['datasets'][0]);
        self::assertIsArray($chartData['datasets'][0]['data']);

        return $chartData['datasets'][0]['data'];
    }
}
