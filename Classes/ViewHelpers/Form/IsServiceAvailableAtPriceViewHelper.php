<?php

declare(strict_types=1);

namespace Extcode\Cart\ViewHelpers\Form;

/*
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
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Is Service Method Available ViewHelper
 */
class IsServiceAvailableAtPriceViewHelper extends AbstractConditionViewHelper
{
    public function initializeArguments(): void
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

    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $service = $arguments['service'];
        $price = $arguments['price'];

        return (bool) $service->isAvailable($price);
    }
}
