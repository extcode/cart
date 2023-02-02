<?php

namespace Extcode\Cart\Domain\Repository\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class ProductRepository extends Repository
{
    /**
     * Find all products
     *
     * @return QueryResultInterface|array
     */
    public function findAll(array $searchArguments = [])
    {
        $query = $this->createQuery();

        $and = $this->getFilterConstraints($searchArguments, $query);
        $and[] = $query->equals('deleted', 0);
        $constraint = $query->logicalAnd(...$and);

        $query->matching($constraint);

        return $query->execute();
    }

    protected function getFilterConstraints(array $searchArguments, QueryInterface $query): array
    {
        $and = [];

        if (isset($searchArguments['filter'])) {
            foreach ((array)$searchArguments['filter'] as $field => $value) {
                if ($field == 'start' && !empty($value)) {
                    $and[] = $query->greaterThan('crdate', strtotime($value));
                } elseif ($field == 'stop' && !empty($value)) {
                    $and[] = $query->lessThan('crdate', strtotime($value));
                }
            }
        }

        return $and;
    }
}
