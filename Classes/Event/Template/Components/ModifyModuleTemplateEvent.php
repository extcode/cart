<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Template\Components;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

class ModifyModuleTemplateEvent
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $settings,
        private readonly ModuleTemplate $moduleTemplate,
    ) {}

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getModuleTemplate(): ModuleTemplate
    {
        return $this->moduleTemplate;
    }
}
