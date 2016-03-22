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
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ProductController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * productRepository
     *
     * @var \Extcode\Cart\Domain\Repository\Product\ProductRepository
     * @inject
     */
    protected $productRepository;

    /**
     * categoryRepository
     *
     * @var \Extcode\Cart\Domain\Repository\CategoryRepository
     * @inject
     */
    protected $categoryRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var int Current page
     */
    protected $pageId;

    /**
     * piVars
     *
     * @var array
     */
    protected $piVars;

    /**
     * Action initializer
     *
     * @return void
     */
    protected function initializeAction()
    {
        $this->pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');

        $frameworkConfiguration =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
        $persistenceConfiguration = array('persistence' => array('storagePid' => $this->pageId));
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));

        $this->piVars = $this->request->getArguments();
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        if ($this->settings['categoriesList']) {
            $selectedCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(
                ',',
                $this->settings['categoriesList'],
                true
            );

            $categories = array();

            foreach ($selectedCategories as $selectedCategory) {
                $category = $this->categoryRepository->findByUid($selectedCategory);
                $categories = array_merge(
                    $categories,
                    $this->categoryRepository->findSubcategoriesRecursiveAsArray($category)
                );
            }

            $products = $this->productRepository->findByCategories($categories);
        } else {
            $products = $this->productRepository->findAll($this->piVars);
        }

        $this->view->assign('piVars', $this->piVars);
        $this->view->assign('products', $products);
    }

    /**
     * action show
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     * @return void
     */
    public function showAction(\Extcode\Cart\Domain\Model\Product\Product $product)
    {
        $this->view->assign('product', $product);
    }

    /**
     * action teaser
     *
     * @return void
     */
    public function teaserAction()
    {
        $products = $this->productRepository->findByUids($this->settings['productUids']);
        $this->view->assign('products', $products);
    }

    /**
     * action flexform
     *
     * @return void
     */
    public function flexformAction()
    {
        $this->contentObj = $this->configurationManager->getContentObject();
        $contentId = $this->contentObj->data['uid'];

        $this->view->assign('contentId', $contentId);
    }
}
