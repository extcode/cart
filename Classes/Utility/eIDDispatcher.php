<?php

namespace Extcode\Cart\Utility;

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
 * eID Dispatcher
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('cart') .
    'Classes/Utility/AjaxDispatcher/Cart.php';

// Init TSFE for database access

$pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('cartPid');
$GLOBALS['TSFE'] = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    'TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController',
    $GLOBALS['TYPO3_CONF_VARS'],
    $pageId,
    0,
    true
);
\TYPO3\CMS\Frontend\Utility\EidUtility::initLanguage();

$GLOBALS['TSFE']->connectToDB();
$GLOBALS['TSFE']->initFEuser();
\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();

$GLOBALS['TSFE']->initUserGroups();
$GLOBALS['TSFE']->determineId();
$GLOBALS['TSFE']->sys_page = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    'TYPO3\CMS\Frontend\Page\PageRepository'
);
$GLOBALS['TSFE']->initTemplate();
$GLOBALS['TSFE']->getConfigArray();

$dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    'Extcode\Cart\Utility\AjaxDispatcher\Cart'
);

// ATTENTION! Dispatcher first needs to be initialized here!!!
echo $dispatcher->init()->dispatch();
