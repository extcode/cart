<?php

declare(strict_types=1);

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
    ) {
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'pageTitle',
            'string',
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
