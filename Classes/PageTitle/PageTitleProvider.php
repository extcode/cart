<?php

declare(strict_types=1);

namespace Extcode\Cart\PageTitle;

use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;

class PageTitleProvider extends AbstractPageTitleProvider
{
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
