<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final class DetailPageLink
{
    public function __construct(
        protected int $pageUid,
        protected string $extensionName = '',
        protected string $pluginName = '',
        protected string $controller = '',
        protected string $action = '',
    ) {}

    public function getPageUid(): int
    {
        return $this->pageUid;
    }

    public function getExtensionName(): string
    {
        return $this->extensionName;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getController(): string
    {
        return $this->controller;
    }
    public function getAction(): string
    {
        return $this->action;
    }

    public function isActionLink(): bool
    {
        if ($this->pageUid && $this->extensionName && $this->pluginName && $this->controller && $this->action) {
            return true;
        }
        return false;
    }
}
