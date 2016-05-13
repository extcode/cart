<?php

namespace Extcode\Cart\Controller\Product;

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
 * Product BeVariant Controller
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariantController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * productRepository
     *
     * @var \Extcode\Cart\Domain\Repository\Product\BeVariantRepository
     * @inject
     */
    protected $beVariantRepository;

    /**
     * Persistence Manager
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

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
        $persistenceConfiguration = ['persistence' => ['storagePid' => $this->pageId]];
        $this->configurationManager->setConfiguration(array_merge($frameworkConfiguration, $persistenceConfiguration));

        $this->piVars = $this->request->getArguments();
    }

    /**
     * action show
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
     * @return void
     */
    public function showAction(\Extcode\Cart\Domain\Model\Product\BeVariant $beVariant)
    {
        $this->view->assign('beVariant', $beVariant);
    }

    /**
     * action edit
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
     * @return void
     */
    public function editAction(\Extcode\Cart\Domain\Model\Product\BeVariant $beVariant)
    {
        $this->view->assign('beVariant', $beVariant);
    }

    /**
     * action update
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
     * @return void
     */
    public function updateAction(\Extcode\Cart\Domain\Model\Product\BeVariant $beVariant)
    {
        $this->beVariantRepository->update($beVariant);

        //$this->persistenceManager->persistAll();

        $this->redirect('show', null, null, ['beVariant' => $beVariant]);
    }
}
