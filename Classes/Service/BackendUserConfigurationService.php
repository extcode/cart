<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Saves and restores the settings of the backend modules in the user configuration (uc) of the current backend user,
 * e.g. the search arguments of the order list.
 */
class BackendUserConfigurationService
{
    public const USER_CONFIGURATION_KEY_ORDER_SEARCH = 'tx_cart_order_search';

    /**
     * The search arguments known by \Extcode\Cart\Domain\Repository\Order\ItemRepository::getFilterConstraints()
     */
    private const ORDER_SEARCH_ARGUMENTS = [
        'customer',
        'orderNumber',
        'orderDateStart',
        'orderDateEnd',
        'invoiceNumber',
        'invoiceDateStart',
        'invoiceDateEnd',
        'paymentStatus',
        'shippingStatus',
    ];

    /**
     * @return array<string, string>
     */
    public function getOrderSearchArguments(): array
    {
        $data = self::getBackendUser()?->uc[self::USER_CONFIGURATION_KEY_ORDER_SEARCH] ?? [];

        if (is_array($data) === false) {
            return [];
        }

        return self::filterOrderSearchArguments($data);
    }

    /**
     * @param array<array-key, mixed> $searchArguments
     */
    public function saveOrderSearchArguments(array $searchArguments): void
    {
        $backendUser = self::getBackendUser();
        if ($backendUser === null) {
            return;
        }

        $backendUser->uc[self::USER_CONFIGURATION_KEY_ORDER_SEARCH] = self::filterOrderSearchArguments($searchArguments);
        $backendUser->writeUC();
    }

    /**
     * @param array<array-key, mixed> $searchArguments
     *
     * @return array<string, string>
     */
    private static function filterOrderSearchArguments(array $searchArguments): array
    {
        $filteredSearchArguments = [];

        foreach (self::ORDER_SEARCH_ARGUMENTS as $name) {
            if (is_scalar($searchArguments[$name] ?? null)) {
                $filteredSearchArguments[$name] = (string) $searchArguments[$name];
            }
        }

        return $filteredSearchArguments;
    }

    private static function getBackendUser(): ?BackendUserAuthentication
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;

        return $backendUser instanceof BackendUserAuthentication ? $backendUser : null;
    }
}
