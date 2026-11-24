<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Updates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;
use Extcode\Cart\Updates\OrderDiscountTaxesUpdate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Output\BufferedOutput;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(OrderDiscountTaxesUpdate::class)]
final class OrderDiscountTaxesUpdateTest extends FunctionalTestCase
{
    use TestingFramework;

    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    private OrderDiscountTaxesUpdate $subject;

    private BufferedOutput $output;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/Fixtures/OrderDiscountTaxes.php');

        $registry = $this->get(UpgradeWizardRegistry::class);
        self::assertInstanceOf(UpgradeWizardRegistry::class, $registry);
        $subject = $registry->getUpgradeWizard('extcodeCartOrderDiscountTaxes');
        self::assertInstanceOf(OrderDiscountTaxesUpdate::class, $subject);
        $this->subject = $subject;
        $this->output = new BufferedOutput();
        $this->subject->setOutput($this->output);
    }

    #[Test]
    public function updateIsNecessaryForDiscountsWithoutTaxesPerTaxClass(): void
    {
        self::assertTrue($this->subject->updateNecessary());
    }

    #[Test]
    public function updateIsNotNecessaryIfAllDiscountsHaveTaxesPerTaxClass(): void
    {
        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_cart_domain_model_order_discount')
            ->executeStatement('UPDATE tx_cart_domain_model_order_discount SET taxes = \'{}\' ')
        ;

        self::assertFalse($this->subject->updateNecessary());
    }

    #[Test]
    public function storesTheTaxesPerTaxClassOfAPercentageCoupon(): void
    {
        self::assertTrue($this->subject->executeUpdate());

        self::assertSame(
            ['taxes' => '{"1":0.38,"2":0.07}', 'tax_class' => 0, 'coupon_type' => CartCouponPercentage::class],
            self::getDiscount(1)
        );
    }

    #[Test]
    public function storesTheTaxAndTheTaxClassOfAFixedCoupon(): void
    {
        self::assertTrue($this->subject->executeUpdate());

        self::assertSame(
            ['taxes' => '{"3":1.9}', 'tax_class' => 3, 'coupon_type' => CartCouponFix::class],
            self::getDiscount(2)
        );
    }

    #[Test]
    public function splitsTheTaxesOfSeveralCouponsByGross(): void
    {
        self::assertTrue($this->subject->executeUpdate());

        self::assertSame(
            ['taxes' => '{"5":0.95}', 'tax_class' => 5, 'coupon_type' => ''],
            self::getDiscount(3)
        );
        self::assertSame(
            ['taxes' => '{"5":1.9}', 'tax_class' => 5, 'coupon_type' => ''],
            self::getDiscount(4)
        );
    }

    #[Test]
    public function skipsOrdersWhoseNumbersDoNotAddUp(): void
    {
        self::assertTrue($this->subject->executeUpdate());

        foreach ([5, 6, 7] as $discountUid) {
            self::assertSame(
                ['taxes' => '', 'tax_class' => 0, 'coupon_type' => ''],
                self::getDiscount($discountUid)
            );
        }

        $output = $this->output->fetch();
        self::assertStringContainsString(
            'Skipped order item 4: the split of the taxes by gross does not match gross minus net of each discount',
            $output
        );
        self::assertStringContainsString(
            'Skipped order item 5: the total price does not match the prices of the cart, the discounts and the services',
            $output
        );
        self::assertStringContainsString(
            'The taxes per tax class of the discounts of 2 order(s) are unknown: 4, 5',
            $output
        );
    }

    #[Test]
    public function keepsDiscountsWhichAlreadyHaveTaxesPerTaxClass(): void
    {
        self::assertTrue($this->subject->executeUpdate());

        self::assertSame(
            ['taxes' => '{"11":1.9}', 'tax_class' => 11, 'coupon_type' => ''],
            self::getDiscount(8)
        );
    }

    /**
     * @return array{taxes: string, tax_class: int, coupon_type: string}
     */
    private static function getDiscount(int $uid): array
    {
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_cart_domain_model_order_discount')
            ->select(['taxes', 'tax_class', 'coupon_type'], 'tx_cart_domain_model_order_discount', ['uid' => $uid])
            ->fetchAssociative()
        ;
        self::assertIsArray($row);
        self::assertTrue($row['taxes'] === null || is_string($row['taxes']));
        self::assertIsNumeric($row['tax_class']);
        self::assertIsString($row['coupon_type']);

        return [
            'taxes' => $row['taxes'] ?? '',
            'tax_class' => (int) $row['tax_class'],
            'coupon_type' => $row['coupon_type'],
        ];
    }
}
