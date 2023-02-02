<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PaymentPaidShippingOpen implements WidgetInterface
{
    /**
     * @var WidgetConfigurationInterface
     */
    private $configuration;

    /**
     * @var ListDataProviderInterface
     */
    private $dataProvider;

    /**
     * @var StandaloneView
     */
    private $view;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        ListDataProviderInterface $dataProvider,
        StandaloneView $view,
        array $options = []
    ) {
        $this->configuration = $configuration;
        $this->dataProvider = $dataProvider;
        $this->view = $view;
        $this->options = [
                'template' => 'Widget/PaymentPaidShippingOpen',
            ] + $options;
    }

    public function renderWidgetContent(): string
    {
        $this->view->setTemplate($this->options['template']);

        $this->view->assignMultiple([
            'items' => $this->dataProvider->getItems(),
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);
        return $this->view->render();
    }
    public function getOptions(): array
    {
        return $this->options;
    }
}
