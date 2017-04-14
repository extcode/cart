<?php

namespace Extcode\Cart\Domain\Repository\Order;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Order Product Repository
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Find all products
     *
     * @param array $arguments Plugin Variables
     *
     * @return QueryResultInterface|array
     */
    public function findAll(array $arguments = [])
    {
        // settings
        $query = $this->createQuery();

        $and = [
            $query->equals('deleted', 0)
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

        $orderItems = $query->execute();
        return $orderItems;
    }
}
