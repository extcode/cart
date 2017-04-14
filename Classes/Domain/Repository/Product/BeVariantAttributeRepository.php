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
 * Product BeVariantAttribute Repository
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariantAttributeRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * Finds objects filtered by $arguments['filter']
     *
     * @param array $arguments
     *
     * @return QueryResultInterface|array
     */
    public function findAll($arguments = [])
    {
        // settings
        $query = $this->createQuery();

        $constraints = [];

        // filter
        if (isset($arguments['filter'])) {
            foreach ((array)$arguments['filter'] as $field => $value) {
                if (empty($value)) {
                    continue;
                }

                switch ($field) {
                    case 'sku':
                        $constraints[] = $query->like('sku', '%' . $value . '%');
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

        $beVariantAttributes = $query->execute();

        return $beVariantAttributes;
    }
}
