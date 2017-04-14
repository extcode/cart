<?php

namespace Extcode\Cart\Controller;

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
 * Product Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * productRepository
     *
     * @var \Extcode\Cart\Domain\Repository\Product\ProductRepository
     */
    protected $productRepository;

    /**
     * categoryRepository
     *
     * @var \Extcode\Cart\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * Search Arguments
     *
     * @var array
     */
    protected $searchArguments;

    /**
     * @param \Extcode\Cart\Domain\Repository\Product\ProductRepository $productRepository
     */
    public function injectProductRepository(
        \Extcode\Cart\Domain\Repository\Product\ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param \Extcode\Cart\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(
        \Extcode\Cart\Domain\Repository\CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Action initializer
     */
    protected function initializeAction()
    {
        if (TYPO3_MODE === 'BE') {
            $pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');

            $frameworkConfiguration =
                $this->configurationManager->getConfiguration(
                    \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
                );
            $persistenceConfiguration = ['persistence' => ['storagePid' => $pageId]];
            $this->configurationManager->setConfiguration(
                array_merge($frameworkConfiguration, $persistenceConfiguration)
            );

            $arguments = $this->request->getArguments();
            if ($arguments['search']) {
                $this->searchArguments = $arguments['search'];
            }
        }
    }

    /**
     * Create the demand object which define which records will get shown
     *
     * @param array $settings
     *
     * @return \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand
     */
    protected function createDemandObjectFromSettings($settings)
    {
        /** @var \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand $demand */
        $demand = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand::class
        );

        if ($this->searchArguments['sku']) {
            $demand->setSku($this->searchArguments['sku']);
        }
        if ($this->searchArguments['title']) {
            $demand->setTitle($this->searchArguments['title']);
        }
        if ($settings['orderBy']) {
            $demand->setOrder($settings['orderBy'] . ' ' . $settings['orderDirection']);
        }

        $this->addCategoriesToDemandObjectFromSettings($demand, $settings);

        return $demand;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Dto\Product\ProductDemand $demand
     * @param array $settings
     */
    protected function addCategoriesToDemandObjectFromSettings(&$demand, $settings)
    {
        if ($this->settings['categoriesList']) {
            $selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(
                ',',
                $this->settings['categoriesList'],
                true
            );

            $categories = [];

            if ($this->settings['listSubcategories']) {
                foreach ($selectedCategories as $selectedCategory) {
                    $category = $this->categoryRepository->findByUid($selectedCategory);
                    $categories = array_merge(
                        $categories,
                        $this->categoryRepository->findSubcategoriesRecursiveAsArray($category)
                    );
                }
            } else {
                $categories = $selectedCategories;
            }

            $demand->setCategories($categories);
        }
    }

    /**
     * action list
     */
    public function listAction()
    {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $products = $this->productRepository->findDemanded($demand);

        $this->view->assign('searchArguments', $this->searchArguments);
        $this->view->assign('products', $products);
    }

    /**
     * action show
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     *
     * @ignorevalidation $product
     */
    public function showAction(\Extcode\Cart\Domain\Model\Product\Product $product = null)
    {
        if (empty($product)) {
            $this->forward('list');
        }

        $this->view->assign('user', $GLOBALS['TSFE']->fe_user->user);
        $this->view->assign('product', $product);
    }

    /**
     * action showForm
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     */
    public function showFormAction(\Extcode\Cart\Domain\Model\Product\Product $product = null)
    {
        if (!$product && $this->request->getPluginName()=='ProductPartial') {
            $requestBuilder =$this->objectManager->get(
                \TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder::class
            );
            $configurationManager = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class
            );
            $configurationManager->setConfiguration([
                'vendorName' => 'Extcode',
                'extensionName' => 'Cart',
                'pluginName' => 'Product',
            ]);
            /**
             * @var \TYPO3\CMS\Extbase\Mvc\Web\Request $cartProductRequest
             */
            $cartProductRequest = $requestBuilder->build();

            if ($cartProductRequest->hasArgument('product')) {
                $productUid = $cartProductRequest->getArgument('product');
            }

            $productRepository = $this->objectManager->get(
                \Extcode\Cart\Domain\Repository\Product\ProductRepository::class
            );

            $product =  $productRepository->findByUid($productUid);
        }
        $this->view->assign('product', $product);
    }

    /**
     * action teaser
     */
    public function teaserAction()
    {
        $products = $this->productRepository->findByUids($this->settings['productUids']);
        $this->view->assign('products', $products);
    }

    /**
     * action flexform
     */
    public function flexformAction()
    {
        $this->contentObj = $this->configurationManager->getContentObject();
        $contentId = $this->contentObj->data['uid'];

        $this->view->assign('contentId', $contentId);
    }
}
