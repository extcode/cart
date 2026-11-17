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
use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractWidgetTestCase extends FunctionalTestCase
{
    use TestingFramework;

    /**
     * Page uid of the sysfolder holding the orders, see Tests/Fixtures/BaseDatabase.php
     */
    protected const ORDER_STORAGE_PID = 105;

    /**
     * Page uid of a second sysfolder for orders, see createSecondStorageFolder()
     */
    protected const SECOND_ORDER_STORAGE_PID = 106;

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

    protected function createOrderItemQuery(): OrderItemQuery
    {
        return new OrderItemQuery(
            $this->getConnectionPool()->getQueryBuilderForTable('tx_cart_domain_model_order_item')
        );
    }

    protected function createStoragePidAccess(): StoragePidAccess
    {
        return new StoragePidAccess($this->createOrderItemQuery());
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
        int $pid = self::ORDER_STORAGE_PID,
    ): void {
        $connectionPool = $this->getConnectionPool();

        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_item')->insert(
            'tx_cart_domain_model_order_item',
            [
                'uid' => $uid,
                'pid' => $pid,
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
     * Creates a second sysfolder for orders next to "Orders", which everybody
     * may show, like its parent page "Shop". "Orders" itself grants no
     * permissions to non-admin users.
     */
    protected function createSecondStorageFolder(): void
    {
        $this->getConnectionPool()->getConnectionForTable('pages')->update(
            'pages',
            ['perms_everybody' => Permission::PAGE_SHOW],
            ['uid' => 101]
        );
        $this->getConnectionPool()->getConnectionForTable('pages')->insert(
            'pages',
            [
                'uid' => self::SECOND_ORDER_STORAGE_PID,
                'pid' => 101,
                'title' => 'More Orders',
                'doktype' => PageRepository::DOKTYPE_SYSFOLDER,
                'perms_everybody' => Permission::PAGE_SHOW,
            ]
        );
    }

    /**
     * Logs in a non-admin backend user with the page "Shop" as mount point.
     */
    protected function setUpEditor(): void
    {
        $this->getConnectionPool()->getConnectionForTable('be_users')->insert(
            'be_users',
            [
                'uid' => 2,
                'pid' => 0,
                'username' => 'editor',
                'password' => '',
                'admin' => 0,
                'db_mountpoints' => '101',
                'options' => 3,
            ]
        );

        $this->setUpBackendUser(2);
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
