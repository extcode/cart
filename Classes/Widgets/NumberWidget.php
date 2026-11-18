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
use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

/**
 * Shows a number like the NumberWithIconWidget of the dashboard, but
 * without repeating the title of the widget. An amount of an
 * AmountDataProviderInterface is shown with currency.
 *
 * Options:
 * - icon: identifier of the icon
 * - title, subtitle: optional texts or LLL references above the number
 * - currency: arguments of the currency ViewHelper (f:format.currency) for
 *   amounts, e.g. currencySign, decimalSeparator, thousandsSeparator,
 *   prependCurrency, separateCurrency, decimals
 */
class NumberWidget implements RequestAwareWidgetInterface, WidgetInterface
{
    private const DEFAULT_CURRENCY = [
        'currencySign' => '€',
        'decimalSeparator' => ',',
        'thousandsSeparator' => '.',
        'prependCurrency' => false,
        'separateCurrency' => true,
        'decimals' => 2,
    ];

    private ServerRequestInterface $request;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly AmountDataProviderInterface|NumberWithIconDataProviderInterface $dataProvider,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly array $options = [],
    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $isAmount = $this->dataProvider instanceof AmountDataProviderInterface;

        $view = $this->backendViewFactory->create($this->request, ['typo3/cms-dashboard', 'extcode/cart']);
        $view->assignMultiple([
            'icon' => $this->options['icon'] ?? '',
            'title' => $this->options['title'] ?? '',
            'subtitle' => $this->options['subtitle'] ?? '',
            'number' => $isAmount ? $this->dataProvider->getAmount() : $this->dataProvider->getNumber(),
            'isAmount' => $isAmount,
            'currency' => array_merge(
                self::DEFAULT_CURRENCY,
                is_array($this->options['currency'] ?? null) ? $this->options['currency'] : []
            ),
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $view->render('Widget/CartNumber');
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
