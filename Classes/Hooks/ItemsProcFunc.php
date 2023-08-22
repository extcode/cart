<?php

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Utility\TemplateLayout;
use Psr\Log\LoggerAwareInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager;
use TYPO3\CMS\Form\Mvc\Configuration\YamlSource;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;
use TYPO3\CMS\Form\Slot\FilePersistenceSlot;

class ItemsProcFunc
{
    /** @var TemplateLayout $templateLayoutsUtility */
    protected $templateLayoutsUtility;

    public function __construct()
    {
        $this->templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_templateLayout(array &$config)
    {
        $pageId = 0;
        $currentColPos = $config['flexParentDatabaseRow']['colPos'];
        $pageId = $this->getPageId($config['flexParentDatabaseRow']['pid']);

        $extKey = $config['config']['extKey'];
        $pluginName = $config['config']['pluginName'];

        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId, $extKey, $pluginName);

            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);
            foreach ($templateLayouts as $layout) {
                $additionalLayout = [
                    htmlspecialchars($this->getLanguageService()->sL($layout[0])),
                    $layout[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }
    }

    public function user_formDefinition(array &$config)
    {
        if ($config['field'] !== 'form_definition') {
            return;
        }

        $prototypeName = 'cart'; 
        if (is_array($config['config']['itemsProcFuncConfig']) && !empty($config['config']['itemsProcFuncConfig']['prototypeName'])) {
            $prototypeName = $config['config']['itemsProcFuncConfig']['prototypeName'];
        }

        $formPersistenceManager = $this->getFormPersistenceManager();
        $availableForms = $formPersistenceManager->listForms();

        foreach ($availableForms as $availableForm) {
            $form = $formPersistenceManager->load($availableForm['persistenceIdentifier']);

            if ($form['prototypeName'] === $prototypeName) {
                $config['items'][] = [
                    0 => $availableForm['name'],
                    1 => $availableForm['persistenceIdentifier'],
                ];
            }
        }
    }

    protected function getExtKey($listType)
    {
        [$ext, $plugin] = explode('_', $listType, 2);

        if (substr($ext, 0, 4) === 'cart') {
            return 'cart_' . substr($ext, 4);
        }

        return $ext;
    }

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     *
     * @param array $templateLayouts
     * @param int $currentColPos
     * @return array
     */
    protected function reduceTemplateLayouts($templateLayouts, $currentColPos)
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];
        foreach ($templateLayouts as $key => $layout) {
            if (is_array($layout[0])) {
                if (isset($layout[0]['allowedColPos']) && \str_ends_with($layout[1], '.')) {
                    $layoutKey = substr($layout[1], 0, -1);
                    $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout[0]['allowedColPos'], true);
                }
            } else {
                $allLayouts[$key] = $layout;
            }
        }
        if (!empty($restrictions)) {
            foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
                if (!in_array($currentColPos, $restrictedColPosList, true)) {
                    unset($allLayouts[$restrictedIdentifier]);
                }
            }
        }

        return $allLayouts;
    }

    /**
     * Get page id, if negative, then it is a "after record"
     *
     * @param int $pid
     * @return int
     */
    protected function getPageId($pid)
    {
        $pid = (int)$pid;

        if ($pid > 0) {
            return $pid;
        }

        $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');
        return $row['pid'];
    }

    /**
     * Returns LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return object|LoggerAwareInterface|SingletonInterface
     */
    protected function getFormPersistenceManager()
    {
        $resourceFactory = GeneralUtility::makeInstance(
            ResourceFactory::class
        );
        $storageRepository = GeneralUtility::makeInstance(
            StorageRepository::class
        );
        $yamlSource = GeneralUtility::makeInstance(
            YamlSource::class
        );

        $filePersistenceSlot = GeneralUtility::makeInstance(
            FilePersistenceSlot::class
        );
        $configurationManager = GeneralUtility::makeInstance(
            ConfigurationManager::class
        );
        $cacheManager = GeneralUtility::makeInstance(
            CacheManager::class
        );

        return GeneralUtility::makeInstance(
            FormPersistenceManager::class,
            $yamlSource,
            $storageRepository,
            $filePersistenceSlot,
            $resourceFactory,
            $configurationManager,
            $cacheManager
        );
    }
}
