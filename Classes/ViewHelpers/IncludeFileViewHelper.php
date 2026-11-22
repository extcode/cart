<?php

declare(strict_types=1);

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\SystemResource\Exception\SystemResourceException;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @deprecated since cart 12.5, will be removed in cart 13.0. Use the view helpers <f:asset.css> and <f:asset.script>
 *             of the TYPO3 core instead.
 *             TODO: 13.0.0 Remove the class, see Deprecation #869.
 */
class IncludeFileViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly TimeTracker $timeTracker,
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'path',
            'string',
            'Path to the CSS/JS file which should be included',
            true
        );
        $this->registerArgument(
            'compress',
            'bool',
            'Not used anymore, TYPO3 does not compress files since v14',
            false,
            false
        );
    }

    /**
     * Include a CSS/JS file
     */
    public function render(): void
    {
        trigger_error(
            'The view helper <cart:includeFile> is deprecated since cart 12.5 and will be removed in cart 13.0.'
            . ' Use <f:asset.css> or <f:asset.script> instead.',
            E_USER_DEPRECATED
        );

        $path = $this->arguments['path'];
        if (!is_string($path)) {
            return;
        }

        try {
            if (mb_strtolower(mb_substr($path, -3)) === '.js') {
                $this->pageRenderer->addJsFile($path);
            } elseif (mb_strtolower(mb_substr($path, -4)) === '.css') {
                $this->pageRenderer->addCssFile($path);
            }
        } catch (SystemResourceException $e) {
            $this->timeTracker->setTSlogMessage($e->getMessage(), LogLevel::ERROR);
        }
    }
}
