<?php

namespace Extcode\Cart\Domain\Repository\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class ProductAdditionalRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Count all by Category
     *
     * @param array $arguments
     * @param string $additionalType
     *
     * @return QueryResultInterface|array
     */
    public function findAllByAdditionalType(array $arguments = [], string $additionalType)
    {
        // settings
        $query = $this->createQuery();

        $and = [
            $query->equals('deleted', 0),
            $query->equals('additionalType', $additionalType)
        ];

        // filter
        if (isset($arguments['filter'])) {
            foreach ((array)$arguments['filter'] as $field => $value) {
                if ($field == 'start' && !empty($value)) {
                    $and[] = $query->greaterThan('crdate', strtotime($value));
                } elseif ($field == 'stop' && !empty($value)) {
                    $and[] = $query->lessThan('crdate', strtotime($value));
                }
            }
        }

        // create constraint
        $constraint = $query->logicalAnd($and);
        $query->matching($constraint);

        $orderProductAdditionals = $query->execute();
        return $orderProductAdditionals;
    }
}
