<?php

namespace Extcode\Cart\ViewHelpers\Form;

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
 * Is Service Method Available ViewHelper
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class IsServiceAvailableAtPriceViewHelper extends AbstractConditionViewHelper implements CompilableInterface
{
    /**
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'service',
            \Extcode\Cart\Domain\Model\Cart\AbstractService::class,
            'Service object to which the check should be applied.'
        );
        $this->registerArgument(
            'price',
            'float',
            'Price at which the availability should be checked.'
        );
    }

    /**
     * @param array|NULL $arguments
     * @return bool
     * @api
     */
    protected static function evaluateCondition($arguments = null)
    {
        $service = $arguments['service'];
        $price = $arguments['price'];
        return (boolean) $service->isAvailable($price);
    }
}
