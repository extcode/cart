<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Tests\Functional\Widgets\AbstractWidgetTestCase;
use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Provider\OrderListButtonProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Backend\Routing\UriBuilder;

#[CoversClass(OrderListButtonProvider::class)]
#[CoversClass(StoragePidAccess::class)]
class OrderListButtonProviderTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSecondStorageFolder();
        $this->createOrder(1, 'today 12:00');
    }

    #[Test]
    public function getLinkLinksToTheOrderListOfTheOnlyStorageFolderWithOrders(): void
    {
        $link = $this->createButtonProvider()->getLink();

        self::assertStringStartsWith('/typo3/module/cart/orders?', $link);
        $parameters = self::getQueryParameters($link);
        self::assertSame((string) self::ORDER_STORAGE_PID, $parameters['id']);
    }

    #[Test]
    public function getLinkSearchesTheOrderListForTheStatusOfTheFilter(): void
    {
        $search = self::getQueryParameters($this->createButtonProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN)->getLink())['search'];

        self::assertIsArray($search);
        self::assertSame('paid', $search['paymentStatus']);
        self::assertSame('open', $search['shippingStatus']);
        self::assertSame('', $search['customer']);
        self::assertSame('', $search['orderNumber']);
    }

    #[Test]
    public function getLinkDoesNotSearchForSeveralStatusOfTheFilter(): void
    {
        $search = self::getQueryParameters($this->createButtonProvider([
            'filter' => ['payment' => ['status' => ['open', 'paid']]],
        ])->getLink())['search'];

        self::assertIsArray($search);
        self::assertSame('', $search['paymentStatus']);
        self::assertSame('', $search['shippingStatus']);
    }

    #[Test]
    public function getLinkIsEmptyIfThereAreOrdersInSeveralStorageFolders(): void
    {
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);

        self::assertSame('', $this->createButtonProvider()->getLink());
    }

    #[Test]
    public function getLinkLinksToTheConfiguredStorageFolder(): void
    {
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);

        $link = $this->createButtonProvider(['storagePids' => [self::SECOND_ORDER_STORAGE_PID]])->getLink();

        self::assertSame((string) self::SECOND_ORDER_STORAGE_PID, self::getQueryParameters($link)['id']);
    }

    #[Test]
    public function getLinkLinksToTheOnlyStorageFolderTheBackendUserMayShow(): void
    {
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);
        $this->setUpEditor();

        $link = $this->createButtonProvider()->getLink();

        self::assertSame((string) self::SECOND_ORDER_STORAGE_PID, self::getQueryParameters($link)['id']);
    }

    #[Test]
    public function getLinkIsEmptyWithoutOrders(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_item')->truncate('tx_cart_domain_model_order_item');

        self::assertSame('', $this->createButtonProvider()->getLink());
    }

    #[Test]
    public function getTitleReturnsTheConfiguredTitle(): void
    {
        self::assertSame(
            'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.button.show_orders',
            $this->createButtonProvider()->getTitle()
        );
        self::assertSame('My orders', $this->createButtonProvider(['title' => 'My orders'])->getTitle());
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createButtonProvider(array $options = []): OrderListButtonProvider
    {
        $uriBuilder = $this->get(UriBuilder::class);
        self::assertInstanceOf(UriBuilder::class, $uriBuilder);

        return new OrderListButtonProvider($this->createStoragePidAccess(), $uriBuilder, $options);
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function getQueryParameters(string $link): array
    {
        parse_str((string) parse_url($link, PHP_URL_QUERY), $parameters);

        return $parameters;
    }
}
