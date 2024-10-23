<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Template\Components;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

final class ModifyButtonBarEvent
{
    private array $buttons = [];

    public function __construct(
        private readonly RequestInterface $request,
        private readonly array $settings,
        private readonly array $searchArguments,
        private readonly ?Item $orderItem = null,
    ) {}

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getSearchArguments(): array
    {
        return $this->searchArguments;
    }

    public function getOrderItem(): ?Item
    {
        return $this->orderItem;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function setButtons(array $buttons): void
    {
        $this->buttons = $buttons;
    }
}
