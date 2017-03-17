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
     * @param \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand $demand
     *
     * @return QueryResultInterface|array
     */
    public function findDemanded(\Extcode\Cart\Domain\Model\Dto\Product\ProductDemand $demand)
    {
        $query = $this->createQuery();

        $constraints = [];

        if ($demand->getSku()) {
            $constraints[] = $query->equals('sku', $demand->getSku());
        }
        if ($demand->getTitle()) {
            $constraints[] = $query->like('title', '%' . $demand->getTitle() . '%');
        }

        if ((!empty($demand->getCategories()))) {
            $categoryConstraints = [];
            foreach ($demand->getCategories() as $category) {
                $categoryConstraints[] = $query->contains('mainCategory', $category);
                $categoryConstraints[] = $query->contains('categories', $category);
            }
            $constraints = $query->logicalOr($categoryConstraints);
        }

        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd($constraints)
            );
        }

        if ($orderings = $this->createOrderingsFromDemand($demand)) {
            $query->setOrderings($orderings);
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

    /**
     * @param \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand $demand
     *
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createOrderingsFromDemand($demand)
    {
        $orderings = [];

        $orderList = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $demand->getOrder(), true);
        
        if (!empty($orderList)) {
            foreach ($orderList as $orderItem) {
                list($orderField, $ascDesc) =
                    \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(' ', $orderItem, true);
                if ($ascDesc) {
                    $orderings[$orderField] = ((strtolower($ascDesc) == 'desc') ?
                        \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING :
                        \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);
                } else {
                    $orderings[$orderField] = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
                }
            }
        }

        return $orderings;
    }
}
