<?php

namespace Extcode\Cart\ViewHelpers\Form;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Facets\CompilableInterface;

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
