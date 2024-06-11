<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PaymentPaidShippingOpen implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;
    private StandaloneView|ViewInterface $view;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly ListDataProviderInterface $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly array $options = []
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $this->view = $this->backendViewFactory->create($this->request);

        $this->view->assignMultiple([
            'items' => $this->dataProvider->getItems(),
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $this->view->render('Widget/CartPaymentPaidShippingOpen');
    }
    public function getOptions(): array
    {
        return $this->options;
    }
}
