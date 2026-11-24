<?php

declare(strict_types=1);

namespace Extcode\Cart\Updates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\ChattyInterface;
use TYPO3\CMS\Core\Upgrades\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;

/**
 * Stores the taxes per tax class of the discounts of existing orders.
 *
 * Before cart 12.8 only the tax of the coupon in its own tax class was stored,
 * which is 0.0 for percentage coupons. The taxes of the coupons per tax class
 * are the difference between the taxes of the products plus the taxes of the
 * services and the total taxes of the order. If an order has several
 * discounts, the taxes are split by their gross.
 *
 * Orders are skipped and listed if the stored numbers do not add up, e.g.
 * because of special services which are not stored in the order, or if the
 * split by gross does not reproduce the tax of each discount (fixed coupons
 * with different tax classes). Their discounts keep unknown taxes.
 */
#[UpgradeWizard('extcodeCartOrderDiscountTaxes')]
final class OrderDiscountTaxesUpdate implements ChattyInterface, UpgradeWizardInterface
{
    /**
     * Amounts are stored as decimal(17,5), sums of rounded values may differ
     * by a few units of the last digit.
     */
    private const TOLERANCE = 0.0001;

    private const TABLE_DISCOUNT = 'tx_cart_domain_model_order_discount';

    private OutputInterface $output;

    public function __construct(
        private readonly ConnectionPool $connectionPool,
    ) {
        $this->output = new NullOutput();
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function getTitle(): string
    {
        return 'Cart: Store the taxes per tax class of the discounts of orders';
    }

    public function getDescription(): string
    {
        return 'Calculates the taxes per tax class, the tax class and the coupon type of the discounts of existing'
            . ' orders from the stored taxes of the order. Orders whose numbers do not add up are skipped and listed.';
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->findOrderItemsWithUnknownDiscountTaxes() !== [];
    }

    public function executeUpdate(): bool
    {
        $skipped = [];
        foreach ($this->findOrderItemsWithUnknownDiscountTaxes() as $orderItemUid) {
            $reason = $this->migrateOrderItem($orderItemUid);
            if ($reason !== null) {
                $skipped[] = $orderItemUid;
                $this->output->writeln(
                    sprintf('Skipped order item %d: %s', $orderItemUid, $reason)
                );
            }
        }

        if ($skipped !== []) {
            $this->output->writeln(
                sprintf(
                    'The taxes per tax class of the discounts of %d order(s) are unknown: %s',
                    count($skipped),
                    implode(', ', $skipped)
                )
            );
        }

        return true;
    }

    /**
     * @return list<int>
     */
    private function findOrderItemsWithUnknownDiscountTaxes(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_DISCOUNT);
        $rows = $queryBuilder
            ->select('item')
            ->distinct()
            ->from(self::TABLE_DISCOUNT)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->isNull('taxes'),
                    $queryBuilder->expr()->eq('taxes', $queryBuilder->createNamedParameter(''))
                )
            )
            ->orderBy('item')
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        return array_map(self::toInt(...), $rows);
    }

    /**
     * @return null|string the reason why the order item was skipped, null if it was migrated
     */
    private function migrateOrderItem(int $orderItemUid): ?string
    {
        $orderItem = $this->fetchRows('tx_cart_domain_model_order_item', 'uid', $orderItemUid)[0] ?? null;
        if ($orderItem === null) {
            return 'the order item does not exist or is deleted';
        }

        $discounts = $this->fetchRows(self::TABLE_DISCOUNT, 'item', $orderItemUid);
        foreach ($discounts as $discount) {
            if (self::toString($discount['taxes']) !== '') {
                return 'some discounts already have taxes per tax class';
            }
        }

        $services = array_merge(
            $this->fetchRows('tx_cart_domain_model_order_shipping', 'item', $orderItemUid),
            $this->fetchRows('tx_cart_domain_model_order_payment', 'item', $orderItemUid)
        );

        $couponGross = self::sum($discounts, 'gross');
        $couponNet = self::sum($discounts, 'net');
        $expectedTotalGross = self::toFloat($orderItem['gross']) - $couponGross + self::sum($services, 'gross');
        $expectedTotalNet = self::toFloat($orderItem['net']) - $couponNet + self::sum($services, 'net');
        if (
            !self::equals($expectedTotalGross, self::toFloat($orderItem['total_gross']))
            || !self::equals($expectedTotalNet, self::toFloat($orderItem['total_net']))
        ) {
            return 'the total price does not match the prices of the cart, the discounts and the services';
        }

        $couponTaxes = $this->calculateCouponTaxes($orderItemUid, $services);
        $taxClassUids = array_map(
            static fn (array $row): int => self::toInt($row['uid']),
            $this->fetchRows('tx_cart_domain_model_order_taxclass', 'item', $orderItemUid)
        );
        foreach ($couponTaxes as $taxClassUid => $tax) {
            if (!in_array($taxClassUid, $taxClassUids, true)) {
                return sprintf('tax class %d does not belong to the order', $taxClassUid);
            }
            if ($tax < 0) {
                return sprintf('the coupon tax of tax class %d is negative', $taxClassUid);
            }
        }
        if (!self::equals(array_sum($couponTaxes), $couponGross - $couponNet)) {
            return 'the taxes do not match gross minus net of the discounts';
        }

        $updates = [];
        foreach ($discounts as $discount) {
            $gross = self::toFloat($discount['gross']);
            $tax = $gross - self::toFloat($discount['net']);

            $taxes = [];
            foreach ($couponTaxes as $taxClassUid => $couponTax) {
                $share = count($discounts) === 1 ? $couponTax : $couponTax * $gross / $couponGross;
                $taxes[$taxClassUid] = round($share, 5);
            }
            if (!self::equals(array_sum($taxes), $tax)) {
                return 'the split of the taxes by gross does not match gross minus net of each discount';
            }

            $updates[self::toInt($discount['uid'])] = [
                'taxes' => json_encode((object) $taxes, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION),
                'tax_class' => self::determineTaxClass($discount, $taxes),
                'coupon_type' => $this->determineCouponType($discount),
            ];
        }

        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_DISCOUNT);
        foreach ($updates as $uid => $values) {
            $connection->update(
                self::TABLE_DISCOUNT,
                $values,
                ['uid' => $uid],
                [
                    'taxes' => Connection::PARAM_STR,
                    'tax_class' => Connection::PARAM_INT,
                    'coupon_type' => Connection::PARAM_STR,
                ]
            );
        }

        return null;
    }

    /**
     * Coupon tax per tax class = tax of the products + tax of the services - total tax.
     *
     * @param list<array<string, mixed>> $services
     *
     * @return array<int, float> uid of the tax class of the order => tax, without zero values
     */
    private function calculateCouponTaxes(int $orderItemUid, array $services): array
    {
        $couponTaxes = [];
        foreach ($this->fetchRows('tx_cart_domain_model_order_tax', 'item', $orderItemUid) as $row) {
            $taxClassUid = self::toInt($row['tax_class']);
            $couponTaxes[$taxClassUid] ??= 0.0;
            if ($row['record_type'] === 'tax') {
                $couponTaxes[$taxClassUid] += self::toFloat($row['tax']);
            } elseif ($row['record_type'] === 'total_tax') {
                $couponTaxes[$taxClassUid] -= self::toFloat($row['tax']);
            }
        }
        foreach ($services as $service) {
            $taxClassUid = self::toInt($service['tax_class']);
            $couponTaxes[$taxClassUid] ??= 0.0;
            $couponTaxes[$taxClassUid] += self::toFloat($service['tax']);
        }

        $couponTaxes = array_map(static fn (float $tax): float => round($tax, 5), $couponTaxes);

        return array_filter($couponTaxes, static fn (float $tax): bool => abs($tax) > self::TOLERANCE);
    }

    /**
     * The tax class of a fixed coupon is the only tax class with a tax. The tax
     * class of a percentage coupon is not used for the calculation and stays
     * unknown.
     *
     * @param array<string, mixed> $discount
     * @param array<int, float> $taxes
     */
    private static function determineTaxClass(array $discount, array $taxes): int
    {
        $tax = self::toFloat($discount['tax']);
        if (count($taxes) !== 1 || abs($tax) <= self::TOLERANCE) {
            return 0;
        }

        $taxClassUid = (int) array_key_first($taxes);

        return self::equals($taxes[$taxClassUid], $tax) ? $taxClassUid : 0;
    }

    /**
     * Takes the type of the coupon record with the same code, if there is
     * exactly one type and it matches the stored tax: a fixed coupon stores its
     * tax, a percentage coupon stores 0.0.
     *
     * @param array<string, mixed> $discount
     */
    private function determineCouponType(array $discount): string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_cart_domain_model_coupon');
        $queryBuilder->getRestrictions()->removeAll();
        $couponTypes = $queryBuilder
            ->select('coupon_type')
            ->distinct()
            ->from('tx_cart_domain_model_coupon')
            ->where(
                $queryBuilder->expr()->eq('code', $queryBuilder->createNamedParameter(self::toString($discount['code'])))
            )
            ->executeQuery()
            ->fetchFirstColumn()
        ;
        if (count($couponTypes) !== 1) {
            return '';
        }

        $couponType = self::toString($couponTypes[0]);
        if ($couponType === 'cartdiscount') {
            $couponType = CartCouponFix::class;
        }

        $tax = self::toFloat($discount['tax']);
        $grossMinusNet = self::toFloat($discount['gross']) - self::toFloat($discount['net']);

        return match (true) {
            $couponType === CartCouponFix::class && self::equals($tax, $grossMinusNet) => $couponType,
            $couponType === CartCouponPercentage::class && self::equals($tax, 0.0) => $couponType,
            default => '',
        };
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function fetchRows(string $table, string $field, int $value): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);

        return $queryBuilder
            ->select('*')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq($field, $queryBuilder->createNamedParameter($value, Connection::PARAM_INT))
            )
            ->orderBy('uid')
            ->executeQuery()
            ->fetchAllAssociative()
        ;
    }

    /**
     * @param list<array<string, mixed>> $rows
     */
    private static function sum(array $rows, string $field): float
    {
        return array_sum(array_map(static fn (array $row): float => self::toFloat($row[$field]), $rows));
    }

    private static function equals(float $a, float $b): bool
    {
        return abs($a - $b) <= self::TOLERANCE;
    }

    private static function toFloat(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private static function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private static function toString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
