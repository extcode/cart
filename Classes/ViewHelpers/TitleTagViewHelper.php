<?php

declare(strict_types=1);

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\ViewHelpers;

use Extcode\Cart\PageTitle\PageTitleProvider;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render the page title
 */
class TitleTagViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly PageTitleProvider $pageTitleProvider,
    ) {}

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'pageTitle',
            'String',
            'The page title'
        );
    }

    public function render(): void
    {
        $pageTitle = $arguments['pageTitle'] ?? '';
        if ($pageTitle !== '') {
            $this->pageTitleProvider->setTitle($pageTitle);
        }
    }
}
