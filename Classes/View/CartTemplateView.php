<?php

namespace Extcode\Cart\View;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\View\TemplateView;

class CartTemplateView extends TemplateView
{
    public function setStep(int $step): void
    {
        $templatePaths = $this->baseRenderingContext->getTemplatePaths();
        $templatePathAndFilename = $templatePaths->resolveTemplateFileForControllerAndActionAndFormat(
            $this->getRenderingContext()->getRequest()->getControllerName(),
            'ShowStep' . $step
        );

        $this->setTemplatePathAndFilename($templatePathAndFilename);
    }
}
