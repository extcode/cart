<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\View;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3Fluid\Fluid\View\ViewInterface;

class ModifyViewEvent
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $settings,
        private readonly ViewInterface $view,
    ) {}

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getView(): ViewInterface
    {
        return $this->view;
    }
}
