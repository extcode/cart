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

/**
 * Product Product Repository
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Finds objects filtered by $piVars['filter']
     *
     * @param array $piVars
     * @return Query Object
     */
    public function findAll($piVars = array())
    {
        // settings
        $query = $this->createQuery();

        $constraints = array();

        // filter
        if (isset($piVars['filter'])) {
            foreach ((array)$piVars['filter'] as $field => $value) {
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
     * Finds objects based on selected categories
     *
     * @param array $categories
     *
     * @return object
     */
    public function findByCategories($categories)
    {
        $query = $this->createQuery();
        //$query->getQuerySettings()->setRespectStoragePage(false);

        $constraints = array();

        if ((!empty($categories))) {
            $categoryConstraints = array();
            foreach ($categories as $category) {
                $categoryConstraints[] = $query->contains('productCategories', $category);
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
     * Finds objects based on selected uids
     *
     * @param string $uids
     *
     * @return object
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
