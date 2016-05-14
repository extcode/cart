<?php

namespace Extcode\Cart\Domain\Repository\Product;

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
 * Product Product Repository
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Find all products filtered by $searchArguments
     *
     * @param array $searchArguments
     *
     * @return QueryResultInterface|array
     */
    public function findAll($searchArguments = [])
    {
        // settings
        $query = $this->createQuery();

        $constraints = [];

        // filter
        if (isset($searchArguments)) {
            foreach ((array)$searchArguments as $field => $value) {
                if (empty($value)) {
                    continue;
                }

                switch ($field) {
                    case 'sku':
                        $constraints[] = $query->equals('sku', $value);
                        break;
                    case 'title':
                        $constraints[] = $query->like('title', '%' . $value . '%');
                }
            }
        }

        // create constraint
        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        $products = $query->execute();

        return $products;
    }

    /**
     * Find all products based on selected categories
     *
     * @param array $categories
     *
     * @return QueryResultInterface|array
     */
    public function findByCategories($categories)
    {
        $query = $this->createQuery();
        //$query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = [];

        if ((!empty($categories))) {
            $categoryConstraints = [];
            foreach ($categories as $category) {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
            $constraints = $query->logicalOr($categoryConstraints);
        }

        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        $result = $query->execute();

        return $result;
    }

    /**
     * Find all products based on selected uids
     *
     * @param string $uids
     *
     * @return QueryResultInterface|array
     */
    public function findByUids($uids)
    {
        $uids = explode(',', $uids);

        $query = $this->createQuery();
        $query->matching(
            $query->in('uid', $uids)
        );

        return $query->execute();
    }
}
