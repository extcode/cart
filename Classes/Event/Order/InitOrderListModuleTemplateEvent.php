<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

use Extcode\Cart\Controller\Backend\Order\OrderController;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

final class InitOrderListModuleTemplateEvent
{
    public function __construct(
        private readonly ModuleTemplate $moduleTemplate,
        private readonly UriBuilder $uriBuilder,
        private readonly OrderController $orderController
    ) {}

    public function getModuleTemplate(): ModuleTemplate
    {
        return $this->moduleTemplate;
    }

    public function getUriBuilder(): UriBuilder
    {
        return $this->uriBuilder;
    }

    public function getOrderController(): OrderController
    {
        return $this->orderController;
    }
}
