<?php

namespace Extcode\Cart\ViewHelpers\Product;

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
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

/**
 * If SpecialPrice Available ViewHelper
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class IfBestSpecialPriceAvailableViewHelper extends AbstractConditionViewHelper implements CompilableInterface
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'product',
            \Extcode\Cart\Domain\Model\Product\Product::class,
            'product for select options',
            true
        );
    }

    /**
     * @param array|NULL $arguments
     * @return bool
     * @api
     */
    protected static function evaluateCondition($arguments = null)
    {
        $product = $arguments['product'];
        $bestSpecialPrice = $product->getBestSpecialPrice(self::getFrontendUserGroupIds());
        return $bestSpecialPrice < $product->getMinPrice();
    }

    /**
     * Get Frontend User Group
     *
     * @return array
     */
    protected static function getFrontendUserGroupIds()
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );

        $feGroupIds = [];
        $feUserId = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
        if ($feUserId) {
            $frontendUserRepository = $objectManager->get(
                \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository::class
            );
            $feUser = $frontendUserRepository->findByUid($feUserId);
            $feGroups = $feUser->getUsergroup();
            if ($feGroups) {
                foreach ($feGroups as $feGroup) {
                    $feGroupIds[] = $feGroup->getUid();
                }
            }
        }
        return $feGroupIds;
    }
}
