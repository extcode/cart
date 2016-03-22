<?php

namespace Extcode\Cart\Domain\Repository;

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
 * Category Repository
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
{

    /**
     * findAllAsRecursiveTreeArray
     *
     * @param \Extcode\Cart\Domain\Model\Category $selectedCategory
     * @return array $categories
     */
    public function findAllAsRecursiveTreeArray($selectedCategory = null)
    {
        $categoriesArray = $this->findAllAsArray($selectedCategory);
        $categoriesTree = $this->buildSubcategories($categoriesArray, null);
        return $categoriesTree;
    }

    /**
     * findAllAsArray
     *
     * @param \Extcode\Cart\Domain\Model\Category $selectedCategory
     * @return array $categories
     */
    public function findAllAsArray($selectedCategory = null)
    {
        $localCategories = $this->findAll();
        $categories = array();
        // Transform categories to array
        foreach ($localCategories as $localCategory) {
            $newCategory = array(
                'uid' => $localCategory->getUid(),
                'title' => $localCategory->getTitle(),
                'parent' =>
                    ($localCategory->getParent() ? $localCategory->getParent()->getUid() : null),
                'subcategories' => null,
                'isSelected' => ($selectedCategory == $localCategory ? true : false)
            );
            $categories[] = $newCategory;
        }
        return $categories;
    }

    /**
     * findSubcategoriesRecursiveAsArray
     *
     * @param Extcode\Cart\Domain\Model\Category $parentCategory
     * @return array $categories
     */
    public function findSubcategoriesRecursiveAsArray($parentCategory)
    {
        $categories = array();
        $localCategories = $this->findAllAsArray();
        foreach ($localCategories as $category) {
            if (($parentCategory && $category['uid'] == $parentCategory->getUid())
                || !$parentCategory
            ) {
                $this->getSubcategoriesIds($localCategories, $category,
                    $categories);
            }
        }
        return $categories;
    }

    /**
     * getSubcategoriesIds
     *
     * @param array $categoriesArray
     * @param array $parentCategory
     * @param array $subcategoriesArray
     * @return void
     */
    private function getSubcategoriesIds(
        $categoriesArray,
        $parentCategory,
        &$subcategoriesArray
    ) {
        $subcategoriesArray[] = $parentCategory['uid'];
        foreach ($categoriesArray as $category) {
            if ($category['parent'] == $parentCategory['uid']) {
                $this->getSubcategoriesIds($categoriesArray, $category,
                    $subcategoriesArray);
            }
        }
    }

    /**
     * buildSubcategories
     *
     * @param array $categoriesArray
     * @param array $parentCategory
     * @return array $categories
     */
    private function buildSubcategories($categoriesArray, $parentCategory)
    {
        $categories = null;
        foreach ($categoriesArray as $category) {
            if ($category['parent'] == $parentCategory['uid']) {
                $newCategory = $category;
                $newCategory['subcategories'] =
                    $this->buildSubcategories($categoriesArray, $category);
                $categories[] = $newCategory;
            }
        }
        return $categories;
    }

}