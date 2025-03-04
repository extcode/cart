<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileException;
use TYPO3\CMS\Core\Resource\Exception\InvalidFileNameException;
use TYPO3\CMS\Core\Resource\Exception\InvalidPathException;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IncludeFileViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly PageRenderer $pageRenderer,
        private readonly TimeTracker $timeTracker,
        private readonly FilePathSanitizer $filePathSanitizer,
    ) {}

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
            'Define if file should be compressed',
            false,
            false
        );
    }

    /**
     * Include a CSS/JS file
     */
    public function render(): void
    {
        $path = $this->arguments['path'];
        $compress = $this->arguments['compress'];

        $pageRenderer = $this->pageRenderer;
        if (ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()) {
            try {
                $path = $this->filePathSanitizer->sanitize((string)$path);
            } catch (InvalidFileNameException) {
                $path = null;
            } catch (InvalidPathException|FileDoesNotExistException|InvalidFileException $e) {
                $path = null;
                if ($GLOBALS['TSFE']->tmpl->tt_track) {
                    $this->timeTracker->setTSlogMessage($e->getMessage(), LogLevel::ERROR);
                }
            }
        }

        if (strtolower(substr((string)$path, -3)) === '.js') {
            $pageRenderer->addJsFile($path, null, $compress);
        } elseif (strtolower(substr((string)$path, -4)) === '.css') {
            $pageRenderer->addCssFile($path, 'stylesheet', 'all', '', $compress);
        }
    }
}
