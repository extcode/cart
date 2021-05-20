<?php

namespace Extcode\Cart\Domain\Repository\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class ItemRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    protected $defaultOrderings = [
        'uid' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    ];

    /**
     * Find a order by a given orderNumber
     *
     * @param string $orderNumber
     *
     * @return QueryResultInterface|array
     */
    public function findOneByOrderNumber(string $orderNumber)
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        $query->getQuerySettings()->setIncludeDeleted(true);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        $query->matching($query->equals('order_number', $orderNumber));
        $orderItem = $query->execute()->getFirst();

        return $orderItem;
    }

    /**
     * Find all orders filtered by $searchArguments
     *
     * @param array $searchArguments
     *
     * @return QueryResultInterface|array
     */
    public function findAll(array $searchArguments = [])
    {
        // settings
        $query = $this->createQuery();

        $and = [
            $query->equals('deleted', 0)
        ];

        // filter
        if (isset($searchArguments)) {
            foreach ((array)$searchArguments as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                switch ($field) {
                    case 'customer':
                        $or = [
                            $query->like('billingAddress.firstName', '%' . $value . '%'),
                            $query->like('billingAddress.lastName', '%' . $value . '%'),
                            $query->like('billingAddress.company', '%' . $value . '%')
                        ];

                        $and[] = $query->logicalOr($or);
                        break;
                    case 'orderNumber':
                        $and[] = $query->like('orderNumber', $value);
                        break;
                    case 'orderDateStart':
                        $and[] = $query->greaterThan('orderDate', strtotime($value));
                        break;
                    case 'orderDateEnd':
                        $and[] = $query->lessThan('orderDate', strtotime($value) + 86400);
                        break;
                    case 'invoiceNumber':
                        $and[] = $query->like('invoiceNumber', $value);
                        break;
                    case 'invoiceDateStart':
                        $and[] = $query->greaterThan('invoiceDate', strtotime($value));
                        break;
                    case 'invoiceDateEnd':
                        $and[] = $query->lessThan('invoiceDate', strtotime($value) + 86400);
                        break;
                    case 'paymentStatus':
                        if ($value !== 'all') {
                            $and[] = $query->equals('payment.status', $value);
                        }
                        break;
                    case 'shippingStatus':
                        if ($value !== 'all') {
                            $and[] = $query->equals('shipping.status', $value);
                        }
                        break;
                }
            }
        }

        // create constraint
        $constraint = $query->logicalAnd($and);
        $query->matching($constraint);

        $orderItems = $query->execute();
        return $orderItems;
    }
}
