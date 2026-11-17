<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Access;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Type\Bitmask\Permission;

/**
 * Restricts the order items of the dashboard widgets to the storage folders
 * the backend user may show (page permissions and mount points). Admins see
 * all orders, without a backend user no orders are shown.
 */
class StoragePidAccess
{
    public function __construct(
        private readonly OrderItemQuery $orderItemQuery,
    ) {
    }

    public function restrict(OrderItemCriteria $criteria, ?BackendUserAuthentication $backendUser = null): OrderItemCriteria
    {
        $backendUser ??= self::getBackendUser();
        if ($backendUser === null) {
            return $criteria->withStoragePids([]);
        }

        if ($backendUser->isAdmin()) {
            return $criteria;
        }

        $storagePids = $criteria->storagePids ?? $this->orderItemQuery->findStoragePids();
        $permissionClause = $backendUser->getPagePermsClause(Permission::PAGE_SHOW);

        return $criteria->withStoragePids(
            array_values(
                array_filter(
                    $storagePids,
                    static fn (int $storagePid): bool => BackendUtility::readPageAccess($storagePid, $permissionClause) !== false
                )
            )
        );
    }

    private static function getBackendUser(): ?BackendUserAuthentication
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;

        return $backendUser instanceof BackendUserAuthentication ? $backendUser : null;
    }
}
