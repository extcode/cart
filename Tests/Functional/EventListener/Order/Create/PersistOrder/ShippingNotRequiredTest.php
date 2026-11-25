<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Order\Fulfillment\FeatureToggle as FulfillmentFeatureToggle;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\ShippingNotRequired;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ShippingNotRequired::class)]
final class ShippingNotRequiredTest extends FunctionalTestCase
{
    use TestingFramework;

    private const ORDER_PRODUCT_TABLE_NAME = 'tx_cart_domain_model_order_product';

    private const ORDER_SHIPPING_TABLE_NAME = 'tx_cart_domain_model_order_shipping';

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    protected array $configurationToUseInTestInstance = [
        'SYS' => [
            'features' => [
                FulfillmentFeatureToggle::NAME => true,
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderItemWithShipping.php');
    }

    #[Test]
    public function theShippingOfAnOrderWithoutShippableProductsIsNotRequired(): void
    {
        $this->persistOrder();

        self::assertSame(ShippingStatus::NOT_REQUIRED, $this->fetchShippingStatus());
    }

    #[Test]
    public function theShippingStatusOfAnOrderWithAShippableProductIsKept(): void
    {
        $this->getConnectionPool()->getConnectionForTable(self::ORDER_PRODUCT_TABLE_NAME)
            ->update(self::ORDER_PRODUCT_TABLE_NAME, ['shippable' => 1], ['uid' => 2])
        ;

        $this->persistOrder();

        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus());
    }

    #[Test]
    public function theShippingStatusIsKeptWithoutTheFeatureToggle(): void
    {
        $configuration = $GLOBALS['TYPO3_CONF_VARS'];
        self::assertIsArray($configuration);
        $GLOBALS['TYPO3_CONF_VARS'] = ArrayUtility::setValueByPath($configuration, ['SYS', 'features', FulfillmentFeatureToggle::NAME], false);

        $this->persistOrder();

        self::assertSame(ShippingStatus::OPEN, $this->fetchShippingStatus());
    }

    private function persistOrder(): void
    {
        $orderItem = GeneralUtility::makeInstance(PersistenceManager::class)->getObjectByIdentifier(1, Item::class);
        self::assertInstanceOf(Item::class, $orderItem);

        $subject = $this->get(ShippingNotRequired::class);
        self::assertInstanceOf(ShippingNotRequired::class, $subject);
        $subject(new PersistOrderEvent(new Cart([]), $orderItem, ['settings' => ['order' => ['pid' => 105]]]));
    }

    private function fetchShippingStatus(): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable(self::ORDER_SHIPPING_TABLE_NAME)
            ->select(['status'], self::ORDER_SHIPPING_TABLE_NAME, ['uid' => 1])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }
}
