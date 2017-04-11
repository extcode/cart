<?php

namespace Extcode\Cart\Hooks;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cart Controller
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class KeSearchIndexer
{
    /**
     * Registers the indexer configuration
     *
     * @param array $params
     * @param $pObj
     */
    public function registerIndexerConfiguration(&$params, $pObj)
    {
        $newArray = [
            'Cart Product Indexer',
            'cartproductindexer',
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('cart') . 'ext_icon.gif'
        ];
        $params['items'][] = $newArray;
    }

    /**
     * custom indexer for ke_search
     *
     * @param array $indexerConfig
     * @param array $indexerObject
     * @return string Output.
     */
    public function customIndexer(&$indexerConfig, &$indexerObject)
    {
        if ($indexerConfig['type'] == 'cartproductindexer') {
            return $this->cartProductIndexer($indexerConfig, $indexerObject);
        }
    }

    /**
     * cart indexer for ke_search
     *
     * @param array $indexerConfig
     * @param array $indexerObject
     *
     * @return string
     */
    public function cartProductIndexer(&$indexerConfig, &$indexerObject)
    {
        $productIndexerName = 'Product Indexer "' . $indexerConfig['title'] . '"';

        $indexPids = $this->getPidList($indexerConfig);
        if ($indexPids === '') {
            $productIndexerMessage = 'ERROR: No Storage Pids configured!';
        } else {
            $products = $this->getProductsToIndex($indexPids);

            if ($products) {
                foreach ($products as $product) {
                    // compile the information which should go into the index
                    // the field names depend on the table you want to index!
                    $sku = strip_tags($product['sku']);
                    $title = strip_tags($product['title']);
                    $teaser = strip_tags($product['teaser']);
                    $description = strip_tags($product['description']);

                    $fullContent = $sku . "\n" . $title . "\n" . $teaser . "\n" . $description;
                    $params = '&tx_cart_product[product]=' . $product['uid'];
                    $tags = '#product#';
                    $additionalFields = array(
                        'sortdate' => $product['crdate'],
                        'orig_uid' => $product['uid'],
                        'orig_pid' => $product['pid'],
                    );

                    $targetPid = $indexerConfig['targetpid'];
                    if (intval($product['cart_product_single_pid'])) {
                        $targetPid = intval($product['cart_product_single_pid']);
                    }

                    $indexerObject->storeInIndex(
                        $indexerConfig['storagepid'], // storage PID
                        $title, // record title
                        'cartproduct', // content type
                        $targetPid, // target PID: where is the single view?
                        $fullContent, // indexed content, includes the title (linebreak after title)
                        $tags, // tags for faceted search
                        $params, // typolink params for singleview
                        $teaser, // abstract; shown in result list if not empty
                        $product['sys_language_uid'], // language uid
                        $product['starttime'], // starttime
                        $product['endtime'], // endtime
                        $product['fe_group'], // fe_group
                        false, // debug only?
                        $additionalFields // additionalFields
                    );
                }
                $productIndexerMessage = 'Success: ' . count($products) . ' products has been indexed.';
            } else {
                $productIndexerMessage = 'Warning: No product found in configured Storage Pids.';
            }
        }

        return '<p><b>' . $productIndexerName . '</b><br/><strong>' . $productIndexerMessage . '</strong></p>';
    }

    /**
     * Returns all Storage Pids for indexing
     *
     * @param $config
     *
     * @return string
     */
    protected function getPidList($config)
    {
        $recursivePids = $this->extendPidListByChildren($config['startingpoints_recursive'], 99);
        if ($config['sysfolder']) {
            return $recursivePids . ',' . $config['sysfolder'];
        } else {
            return $recursivePids;
        }
    }

    /**
     * Find all ids from given ids and level
     *
     * @param string $pidList
     * @param int $recursive
     *
     * @return string
     */
    protected function extendPidListByChildren($pidList = '', $recursive = 0)
    {
        $recursive = (int)$recursive;

        if ($recursive <= 0) {
            return $pidList;
        }

        $queryGenerator = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Database\QueryGenerator::class
        );
        $recursiveStoragePids = $pidList;
        $storagePids = GeneralUtility::intExplode(',', $pidList);
        foreach ($storagePids as $startPid) {
            $pids = $queryGenerator->getTreeList($startPid, $recursive, 0, 1);
            if (strlen($pids) > 0) {
                $recursiveStoragePids .= ',' . $pids;
            }
        }
        return $recursiveStoragePids;
    }

    /**
     * Returns all products for a given PidList
     *
     * @param string $indexPids
     *
     * @return array
     */
    protected function getProductsToIndex($indexPids)
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            'TYPO3\CMS\Extbase\Object\ObjectManager'
        );
        $productRepository = $objectManager->get(
            \Extcode\Cart\Domain\Repository\Product\ProductRepository::class
        );
        $products = $productRepository->findAllForIndexer($indexPids);

        return $products;
    }
}
