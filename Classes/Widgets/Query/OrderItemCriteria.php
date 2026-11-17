<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Restrictions for the order items shown in the dashboard widgets.
 */
final readonly class OrderItemCriteria
{
    private const STATUS_TYPES = ['payment', 'shipping'];

    /**
     * @param array<string, string> $statusFilter status per type (payment, shipping)
     * @param null|list<int> $storagePids storage folders of the orders, null for all
     */
    public function __construct(
        public array $statusFilter = [],
        public ?array $storagePids = null,
    ) {
    }

    /**
     * Creates the criteria from the options of a data provider:
     * - filter: status of payment and/or shipping an order must have, e.g.
     *   ['payment' => ['status' => 'paid'], 'shipping' => ['status' => 'open']]
     * - storagePids: list or comma separated list of storage folders (page
     *   uids) of the orders, all storage folders if empty
     *
     * @param array<string, mixed> $options
     */
    public static function fromOptions(array $options): self
    {
        $storagePids = self::getStoragePids($options['storagePids'] ?? []);

        return new self(
            self::getStatusFilter($options['filter'] ?? []),
            $storagePids === [] ? null : $storagePids
        );
    }

    /**
     * @param null|list<int> $storagePids
     */
    public function withStoragePids(?array $storagePids): self
    {
        return new self($this->statusFilter, $storagePids);
    }

    /**
     * @return array<string, string>
     */
    private static function getStatusFilter(mixed $filter): array
    {
        if (!is_array($filter)) {
            return [];
        }

        $statusFilter = [];
        foreach (self::STATUS_TYPES as $type) {
            $status = is_array($filter[$type] ?? null) ? ($filter[$type]['status'] ?? '') : '';
            if (is_string($status) && $status !== '') {
                $statusFilter[$type] = $status;
            }
        }

        return $statusFilter;
    }

    /**
     * @return list<int>
     */
    private static function getStoragePids(mixed $storagePids): array
    {
        if (is_string($storagePids) || is_int($storagePids)) {
            $storagePids = explode(',', (string) $storagePids);
        }
        if (!is_array($storagePids)) {
            return [];
        }

        $validStoragePids = [];
        foreach ($storagePids as $storagePid) {
            if (is_numeric($storagePid) && (int) $storagePid > 0) {
                $validStoragePids[] = (int) $storagePid;
            }
        }

        return array_values(array_unique($validStoragePids));
    }
}
