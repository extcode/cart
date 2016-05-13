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
 * Product BeVariantAttributeOption Repository
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariantAttributeOptionRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * Finds objects filtered by $piVars['filter']
     *
     * @param array $piVars
     * @return Query Object
     */
    public function findAll($piVars = [])
    {
        // settings
        $query = $this->createQuery();

        $constraints = [];

        // filter
        if (isset($piVars['filter'])) {
            foreach ((array)$piVars['filter'] as $field => $value) {
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
