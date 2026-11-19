<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The payment and shipping status in the orders backend module.
 */
abstract class AbstractOrderStatusTestCase extends FunctionalTestCase
{
    use BackendModuleTrait;

    use TestingFramework;

    /**
     * Page uid of the sysfolder holding the orders, see Tests/Fixtures/BaseDatabase.php
     */
    protected const ORDER_STORAGE_PID = 105;

    /**
     * @var list<non-empty-string> extensions registering own status in their Configuration/OrderStatus.php
     */
    protected array $orderStatusExtensionsToLoad = [];

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
            ...$this->orderStatusExtensionsToLoad,
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-beuser',
            'typo3/cms-core',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../../../Fixtures/BackendUser.php');

        $this->setUpBackendUser(1);

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('en');
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['LANG'],
            $GLOBALS['TYPO3_REQUEST']
        );

        parent::tearDown();
    }

    /**
     * @param array<string, string> $searchArguments the filters of OrderController::listAction
     */
    protected static function dispatchListAction(array $searchArguments = []): ResponseInterface
    {
        $queryParameters = [
            'id' => self::ORDER_STORAGE_PID,
        ];

        if ($searchArguments !== []) {
            $queryParameters['search'] = $searchArguments;
        }

        return self::dispatchRequestWithQueryParameters($queryParameters);
    }

    protected static function dispatchShowAction(int $orderItemUid): ResponseInterface
    {
        return self::dispatchRequestWithQueryParameters(
            [
                'id' => self::ORDER_STORAGE_PID,
                'orderItem' => $orderItemUid,
            ],
            'cart_cart_orders.Backend\Order\Order_show'
        );
    }

    /**
     * @return string[] the order numbers in the order they are rendered in the list
     */
    protected static function getRenderedOrderNumbers(string $html): array
    {
        // the filter renders the search arguments as well, only the order list is of interest here
        $orderList = mb_strstr($html, '<table') ?: '';

        preg_match_all('/O-\d+/', $orderList, $matches);

        return $matches[0];
    }

    protected static function normalizeWhitespace(string $html): string
    {
        return mb_trim((string) preg_replace('/\s+/', ' ', $html));
    }

    protected function createOrder(int $uid, string $paymentStatus, string $shippingStatus): void
    {
        $connectionPool = $this->getConnectionPool();

        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_item')->insert(
            'tx_cart_domain_model_order_item',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'billing_address' => 1,
                'payment' => 1,
                'shipping' => 1,
                'order_number' => sprintf('O-%03d', $uid),
                'order_date' => 1769034161,
                'currency' => '€',
                'total_gross' => 119.0,
                'total_net' => 100.0,
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_address')->insert(
            'tx_cart_domain_model_order_address',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'record_type' => '\\' . BillingAddress::class,
                'first_name' => 'Arthur',
                'last_name' => 'Dent',
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_payment')->insert(
            'tx_cart_domain_model_order_payment',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'name' => 'Payment ' . $uid,
                'status' => $paymentStatus,
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_shipping')->insert(
            'tx_cart_domain_model_order_shipping',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'name' => 'Shipping ' . $uid,
                'status' => $shippingStatus,
            ]
        );
    }
}
