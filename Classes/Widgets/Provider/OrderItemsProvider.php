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
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        QueryBuilder $queryBuilder,
        array $options = []
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->options = [
                'order_by' => 'tx_cart_domain_model_order_item.crdate',
                'order_order' => 'desc',
                'limit' => 10,
            ] + $options;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $constraints = [];

        $this->queryBuilder
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

        $this->queryBuilder
            ->leftJoin(
                'tx_cart_domain_model_order_item',
                'tx_cart_domain_model_order_payment',
                'payment',
                $this->queryBuilder->expr()->eq(
                    'tx_cart_domain_model_order_item.uid',
                    $this->queryBuilder->quoteIdentifier('payment.item')
                )
            )
            ->addSelect('payment.name AS payment_name', 'payment.status AS payment_status');

        if (is_array($this->options['filter']) && is_array($this->options['filter']['payment']) && !empty($this->options['filter']['payment']['status'])) {
            $constraints[] = $this->queryBuilder->expr()->eq(
                'payment.status',
                $this->queryBuilder->createNamedParameter(
                    $this->options['filter']['payment']['status'],
                    Connection::PARAM_STR
                )
            );
        }

        $this->queryBuilder
            ->leftJoin(
                'tx_cart_domain_model_order_item',
                'tx_cart_domain_model_order_shipping',
                'shipping',
                $this->queryBuilder->expr()->eq(
                    'tx_cart_domain_model_order_item.uid',
                    $this->queryBuilder->quoteIdentifier('shipping.item')
                )
            )
            ->addSelect('shipping.name AS shipping_name', 'shipping.status AS shipping_status');

        if (is_array($this->options['filter']) && is_array($this->options['filter']['shipping']) && !empty($this->options['filter']['shipping']['status'])) {
            $constraints[] = $this->queryBuilder->expr()->eq(
                'shipping.status',
                $this->queryBuilder->createNamedParameter(
                    $this->options['filter']['shipping']['status'],
                    Connection::PARAM_STR
                )
            );
        }

        if ($constraints !== []) {
            $this->queryBuilder->where(... $constraints);
        }

        return $this->queryBuilder->executeQuery()->fetchAllAssociative();
    }
}
