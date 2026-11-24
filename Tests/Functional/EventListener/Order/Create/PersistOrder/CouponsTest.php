<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\CartCouponInterface;
use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;
use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Domain\Model\Order\Discount;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\TaxClass as OrderTaxClass;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Coupons;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\TaxClasses;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The cart contains a shirt (23.80 gross, 3.80 tax at 19 %) and a book
 * (10.70 gross, 0.70 tax at 7 %).
 */
#[CoversClass(Coupons::class)]
final class CouponsTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    #[Test]
    public function storesTheTaxesPerTaxClassOfAPercentageCoupon(): void
    {
        $orderTaxClassUids = $this->persistOrder(
            new CartCouponPercentage('10 %', 'percentage', CartCouponPercentage::class, 10.0, self::normal(), 0.0)
        );

        $row = self::getDiscountRow();
        self::assertSame(CartCouponPercentage::class, $row['coupon_type']);
        self::assertSame($orderTaxClassUids['normal'], $row['tax_class']);
        self::assertEqualsWithDelta(3.45, $row['gross'], 0.00001);
        self::assertEqualsWithDelta(3.00, $row['net'], 0.00001);
        self::assertEqualsWithDelta(0.0, $row['tax'], 0.00001);
        self::assertTaxes(
            [
                $orderTaxClassUids['normal'] => 0.38,
                $orderTaxClassUids['reduced'] => 0.07,
            ],
            json_decode($row['taxes'], true)
        );
    }

    #[Test]
    public function storesTheTaxOfAFixedCouponInItsTaxClass(): void
    {
        $orderTaxClassUids = $this->persistOrder(
            new CartCouponFix('11.90', 'fix', 'cartdiscount', 11.90, self::normal(), 0.0)
        );

        $row = self::getDiscountRow();
        self::assertSame(CartCouponFix::class, $row['coupon_type']);
        self::assertSame($orderTaxClassUids['normal'], $row['tax_class']);
        self::assertEqualsWithDelta(1.90, $row['tax'], 0.00001);
        self::assertTaxes(
            [
                $orderTaxClassUids['normal'] => 1.90,
            ],
            json_decode($row['taxes'], true)
        );
    }

    #[Test]
    public function mapsTheTaxClassAndTheTaxesInTheModel(): void
    {
        $orderTaxClassUids = $this->persistOrder(
            new CartCouponPercentage('10 %', 'percentage', CartCouponPercentage::class, 10.0, self::normal(), 0.0)
        );
        $this->getService(PersistenceManager::class)->clearState();

        $discount = $this->getService(PersistenceManager::class)->getObjectByIdentifier(
            self::getDiscountRow()['uid'],
            Discount::class
        );

        self::assertInstanceOf(Discount::class, $discount);
        self::assertSame($orderTaxClassUids['normal'], $discount->getTaxClass()?->getUid());
        self::assertSame('19', $discount->getTaxClass()->getValue());
        self::assertSame(CartCouponPercentage::class, $discount->getCouponType());
        self::assertTrue($discount->hasTaxes());
        self::assertTaxes(
            [
                $orderTaxClassUids['normal'] => 0.38,
                $orderTaxClassUids['reduced'] => 0.07,
            ],
            $discount->getTaxes()
        );
    }

    /**
     * Runs the listeners of the PersistOrderEvent which persist the tax
     * classes and the coupons.
     *
     * @return array<string, int> name of the tax class => uid of the tax class of the order
     */
    private function persistOrder(CartCouponInterface $coupon): array
    {
        $taxClasses = [
            1 => self::normal(),
            2 => new TaxClass(2, '7', 0.07, 'reduced'),
        ];
        $cart = new Cart($taxClasses);
        $cart->addProduct(new Product('simple', 1, 'shirt', 'Shirt', 23.80, $taxClasses[1], 1));
        $cart->addProduct(new Product('simple', 2, 'book', 'Book', 10.70, $taxClasses[2], 1));
        $cart->addCoupon($coupon);

        $orderItem = new Item();
        $orderItem->setPid(1);
        $this->getService(ItemRepository::class)->add($orderItem);
        $this->getService(PersistenceManager::class)->persistAll();

        $event = new PersistOrderEvent($cart, $orderItem, ['settings' => ['order' => ['pid' => 1]]]);
        $this->getService(TaxClasses::class)($event);
        $this->getService(Coupons::class)($event);

        $orderTaxClassUids = [];
        foreach ($event->getTaxClasses() as $orderTaxClass) {
            self::assertInstanceOf(OrderTaxClass::class, $orderTaxClass);
            $orderTaxClassUids[$orderTaxClass->getTitle()] = (int) $orderTaxClass->getUid();
        }

        return $orderTaxClassUids;
    }

    private static function normal(): TaxClass
    {
        return new TaxClass(1, '19', 0.19, 'normal');
    }

    /**
     * @return array{uid: int, gross: float, net: float, tax: float, tax_class: int, coupon_type: string, taxes: string}
     */
    private static function getDiscountRow(): array
    {
        $rows = (new ConnectionPool())
            ->getConnectionForTable('tx_cart_domain_model_order_discount')
            ->select(['*'], 'tx_cart_domain_model_order_discount')
            ->fetchAllAssociative()
        ;
        self::assertCount(1, $rows);
        $row = $rows[0];
        foreach (['uid', 'gross', 'net', 'tax', 'tax_class'] as $field) {
            self::assertIsNumeric($row[$field]);
        }
        self::assertIsString($row['coupon_type']);
        self::assertIsString($row['taxes']);

        return [
            'uid' => (int) $row['uid'],
            'gross' => (float) $row['gross'],
            'net' => (float) $row['net'],
            'tax' => (float) $row['tax'],
            'tax_class' => (int) $row['tax_class'],
            'coupon_type' => $row['coupon_type'],
            'taxes' => $row['taxes'],
        ];
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    private function getService(string $className): object
    {
        $service = $this->get($className);
        self::assertInstanceOf($className, $service);

        return $service;
    }

    /**
     * @param array<int, float> $expected
     */
    private static function assertTaxes(array $expected, mixed $actual): void
    {
        self::assertIsArray($actual);
        self::assertSame(array_keys($expected), array_map(intval(...), array_keys($actual)));
        foreach ($expected as $taxClassUid => $tax) {
            self::assertEqualsWithDelta($tax, $actual[$taxClassUid], 0.00001);
        }
    }
}
