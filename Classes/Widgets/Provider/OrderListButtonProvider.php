<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Backend\Order\OrderController;
use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;

/**
 * Button linking to the order list in the backend module, if the backend
 * user may show exactly one storage folder with orders. The order list is
 * searched for the status of the filter, if the filter has one status for
 * payment or shipping.
 *
 * Options:
 * - filter, storagePids: see OrderItemCriteria::fromOptions(), like the
 *   options of the data provider of the widget
 * - title: text or LLL reference of the button
 */
class OrderListButtonProvider implements ButtonProviderInterface
{
    private const MODULE = 'cart_cart_orders';

    private const SEARCH_ARGUMENTS = [
        'payment' => 'paymentStatus',
        'shipping' => 'shippingStatus',
    ];

    /**
     * @var array<string, mixed>
     */
    private readonly array $options;

    private ?string $link = null;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly StoragePidAccess $storagePidAccess,
        private readonly UriBuilder $uriBuilder,
        array $options = [],
    ) {
        $this->options = array_merge(
            [
                'title' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.button.show_orders',
            ],
            $options
        );
    }

    public function getTitle(): string
    {
        $title = $this->options['title'];

        return is_string($title) ? $title : '';
    }

    /**
     * @return string link to the order list, empty if the backend user may
     *                show no or several storage folders with orders
     */
    public function getLink(): string
    {
        return $this->link ??= $this->createLink();
    }

    public function getTarget(): string
    {
        return '';
    }

    private function createLink(): string
    {
        $criteria = OrderItemCriteria::fromOptions($this->options);

        $storagePids = $this->storagePidAccess->findStoragePids($criteria);
        if (count($storagePids) !== 1) {
            return '';
        }

        // All search arguments are given, so former searches of the backend
        // user in the order list do not restrict the list.
        $search = array_fill_keys(OrderController::ALLOWED_SEARCH_ARGUMENTS, '');
        foreach (self::SEARCH_ARGUMENTS as $type => $argument) {
            $status = $criteria->statusFilter[$type] ?? [];
            if (count($status) === 1) {
                $search[$argument] = $status[0];
            }
        }

        return (string) $this->uriBuilder->buildUriFromRoute(
            self::MODULE,
            [
                'id' => $storagePids[0],
                'search' => $search,
            ]
        );
    }
}
