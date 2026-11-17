<?php

declare(strict_types=1);

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\Widgets\Provider;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

class OrderItemsProvider implements ListDataProviderInterface
{
    private array $options;

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        array $options = []
    ) {
        $this->options = array_merge(
            [
                'order_by' => 'tx_cart_domain_model_order_item.crdate',
                'order_order' => 'desc',
                'limit' => 10,
            ],
            $options
        );
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $queryBuilder = clone $this->queryBuilder;
        $constraints = [];

        $queryBuilder
            ->select(
                'tx_cart_domain_model_order_item.uid',
                'tx_cart_domain_model_order_item.pid',
                'tx_cart_domain_model_order_item.order_number',
                'tx_cart_domain_model_order_item.order_date',
                'tx_cart_domain_model_order_item.invoice_number',
                'tx_cart_domain_model_order_item.invoice_date'
            )
            ->from('tx_cart_domain_model_order_item')
            ->orderBy($this->options['order_by'], $this->options['order_order'])
            ->setMaxResults($this->options['limit']);

        $filter = $this->options['filter'] ?? [];
        foreach (['payment', 'shipping'] as $type) {
            $queryBuilder
                ->leftJoin(
                    'tx_cart_domain_model_order_item',
                    'tx_cart_domain_model_order_' . $type,
                    $type,
                    $queryBuilder->expr()->eq(
                        'tx_cart_domain_model_order_item.uid',
                        $queryBuilder->quoteIdentifier($type . '.item')
                    )
                )
                ->addSelect($type . '.name AS ' . $type . '_name', $type . '.status AS ' . $type . '_status');

            $status = is_array($filter) && is_array($filter[$type] ?? null) ? ($filter[$type]['status'] ?? '') : '';
            if (!is_string($status) || $status === '') {
                continue;
            }

            $constraints[] = $queryBuilder->expr()->eq(
                $type . '.status',
                $queryBuilder->createNamedParameter($status, Connection::PARAM_STR)
            );
        }

        if ($constraints !== []) {
            $queryBuilder->where(...$constraints);
        }

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }
}
