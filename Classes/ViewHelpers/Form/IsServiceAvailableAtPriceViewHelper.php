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

use Extcode\Cart\Domain\Model\Cart\ServiceInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Is Service Method Available ViewHelper
 */
class IsServiceAvailableAtPriceViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'service',
            ServiceInterface::class,
            'Service object to which the check should be applied.'
        );
        $this->registerArgument(
            'price',
            'float',
            'Price at which the availability should be checked.'
        );
    }

    /**
     * @param array|null $arguments
     * @return bool
     * @api
     */
    protected static function evaluateCondition($arguments = null)
    {
        $service = $arguments['service'];
        $price = $arguments['price'];
        return (bool)$service->isAvailable($price);
    }
}
