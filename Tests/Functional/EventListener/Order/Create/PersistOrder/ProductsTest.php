<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Closure;
use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Model\Cart\BeVariant;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product as CartProduct;
use Extcode\Cart\Domain\Model\Cart\TaxClass as CartTaxClass;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\TaxClass;
use Extcode\Cart\Domain\Repository\Order\ProductAdditionalRepository;
use Extcode\Cart\Domain\Repository\Order\ProductRepository;
use Extcode\Cart\Event\Order\BeforePersistOrderProductEvent;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Products;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(Products::class)]
#[CoversClass(BeforePersistOrderProductEvent::class)]
final class ProductsTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private CartTaxClass $taxClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderItem.php');

        $this->taxClass = new CartTaxClass(1, '19', 0.19, 'normal');
    }

    #[Test]
    public function physicalProductIsShippableCancelableAndReturnable(): void
    {
        $this->persistProducts(new CartProduct('simple', 1, 'shirt', 'Shirt', 10.0, $this->taxClass, 1));

        self::assertSame(
            [
                ['sku' => 'shirt', 'shippable' => 1, 'cancelable' => 1, 'returnable' => 1],
            ],
            $this->getStoredProperties()
        );
    }

    #[Test]
    public function virtualProductIsNotShippable(): void
    {
        $cartProduct = new CartProduct('simple', 1, 'download', 'Download', 10.0, $this->taxClass, 1);
        $cartProduct->setIsVirtualProduct(true);

        $this->persistProducts($cartProduct);

        self::assertSame(
            [
                ['sku' => 'download', 'shippable' => 0, 'cancelable' => 1, 'returnable' => 1],
            ],
            $this->getStoredProperties()
        );
    }

    #[Test]
    public function backendVariantGetsThePropertiesOfItsProduct(): void
    {
        $cartProduct = new CartProduct('simple', 1, 'ticket', 'Ticket', 10.0, $this->taxClass, 0);
        $cartProduct->setIsVirtualProduct(true);
        $cartProduct->addBeVariant(new BeVariant('1', $cartProduct, 'Adult', 'adult', 0, 10.0, 2));

        $this->persistProducts($cartProduct);

        self::assertSame(
            [
                ['sku' => 'ticket-adult', 'shippable' => 0, 'cancelable' => 1, 'returnable' => 1],
            ],
            $this->getStoredProperties()
        );
    }

    #[Test]
    public function listenersOfTheEventCanChangeTheProperties(): void
    {
        $events = [];
        $this->addEventListener(
            BeforePersistOrderProductEvent::class,
            static function (BeforePersistOrderProductEvent $event) use (&$events): void {
                $events[] = $event;
                if ($event->getCartProduct()->getSku() !== 'personalized') {
                    return;
                }
                $event->setShippable(false);
                $event->setCancelable(false);
                $event->setReturnable(false);
            }
        );

        $this->persistProducts(
            new CartProduct('simple', 1, 'shirt', 'Shirt', 10.0, $this->taxClass, 1),
            new CartProduct('simple', 2, 'personalized', 'Personalized', 10.0, $this->taxClass, 1)
        );

        self::assertSame(
            [
                ['sku' => 'shirt', 'shippable' => 1, 'cancelable' => 1, 'returnable' => 1],
                ['sku' => 'personalized', 'shippable' => 0, 'cancelable' => 0, 'returnable' => 0],
            ],
            $this->getStoredProperties()
        );
        self::assertCount(2, $events, 'The event must be dispatched once per ordered product.');
        self::assertSame(1, $events[0]->getOrderItem()->getUid());
        self::assertSame('shirt', $events[0]->getOrderProduct()->getSku());
        self::assertNull($events[0]->getBeVariant());
    }

    #[Test]
    public function eventPassesTheBackendVariant(): void
    {
        $events = [];
        $this->addEventListener(
            BeforePersistOrderProductEvent::class,
            static function (BeforePersistOrderProductEvent $event) use (&$events): void {
                $events[] = $event;
            }
        );
        $cartProduct = new CartProduct('simple', 1, 'shirt', 'Shirt', 10.0, $this->taxClass, 0);
        $cartProduct->addBeVariant(new BeVariant('1', $cartProduct, 'Size M', 'm', 0, 10.0, 1));

        $this->persistProducts($cartProduct);

        self::assertCount(1, $events);
        self::assertSame($cartProduct, $events[0]->getCartProduct());
        self::assertSame('shirt-m', $events[0]->getBeVariant()?->getCompleteSku());
        self::assertSame('shirt-m', $events[0]->getOrderProduct()->getSku());
    }

    #[Test]
    public function eventIsDispatchedWhenTheConstructorIsCalledWithoutEventDispatcher(): void
    {
        $events = [];
        $this->addEventListener(
            BeforePersistOrderProductEvent::class,
            static function (BeforePersistOrderProductEvent $event) use (&$events): void {
                $events[] = $event;
                $event->setReturnable(false);
            }
        );
        $subject = new Products(
            GeneralUtility::makeInstance(PersistenceManager::class),
            GeneralUtility::makeInstance(ProductRepository::class),
            GeneralUtility::makeInstance(ProductAdditionalRepository::class)
        );

        $this->persistProductsWith($subject, new CartProduct('simple', 1, 'shirt', 'Shirt', 10.0, $this->taxClass, 1));

        self::assertCount(1, $events);
        self::assertSame(
            [
                ['sku' => 'shirt', 'shippable' => 1, 'cancelable' => 1, 'returnable' => 0],
            ],
            $this->getStoredProperties()
        );
    }

    private function persistProducts(CartProduct ...$cartProducts): void
    {
        $subject = $this->get(Products::class);
        self::assertInstanceOf(Products::class, $subject);
        $this->persistProductsWith($subject, ...$cartProducts);
    }

    private function persistProductsWith(Products $subject, CartProduct ...$cartProducts): void
    {
        $cart = new Cart([1 => $this->taxClass]);
        foreach ($cartProducts as $cartProduct) {
            $cart->addProduct($cartProduct);
        }

        $persistenceManager = GeneralUtility::makeInstance(PersistenceManager::class);
        $orderItem = $persistenceManager->getObjectByIdentifier(1, Item::class);
        self::assertInstanceOf(Item::class, $orderItem);
        $orderTaxClass = $persistenceManager->getObjectByIdentifier(1, TaxClass::class);
        self::assertInstanceOf(TaxClass::class, $orderTaxClass);

        $event = new PersistOrderEvent($cart, $orderItem, ['settings' => ['order' => ['pid' => 105]]]);
        $event->setTaxClasses([1 => $orderTaxClass]);

        $subject($event);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getStoredProperties(): array
    {
        $table = 'tx_cart_domain_model_order_product';

        return $this->getConnectionPool()->getConnectionForTable($table)
            ->select(['sku', 'shippable', 'cancelable', 'returnable'], $table, [], [], ['uid' => 'ASC'])
            ->fetchAllAssociative()
        ;
    }

    private function addEventListener(string $eventClass, Closure $listener): void
    {
        $container = $this->getContainer();
        assert($container instanceof Container);

        $identifier = 'cart-test--event-listener--' . md5($eventClass);
        $container->set($identifier, $listener);
        GeneralUtility::makeInstance(ListenerProvider::class)->addListener($eventClass, $identifier);
    }
}
