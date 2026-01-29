<?php

declare(strict_types=1);

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\Widgets\Provider;

use Extcode\Cart\Constants;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class TurnoverPerDayProvider implements ChartDataProviderInterface
{
    private readonly LanguageService $languageService;
    private array $options;

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
        array $options = []
    ) {
        $this->languageService = GeneralUtility::makeInstance(LanguageServiceFactory::class)->createFromUserPreferences($GLOBALS['BE_USER']);
        $this->options = array_merge(
            [
                'fieldName' => 'tx_cart_domain_model_order_item.order_date',
                'days' => 28,
                'sum' => 'gross',
            ],
            $options
        );
    }

    public function getChartData(): array
    {
        [$labels, $data] = $this->calculateData();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors()[0],
                    'border' => 0,
                    'data' => $data,
                ],
            ],
        ];
    }

    private function calculateData(): array
    {
        $days = (int)$this->options['days'];
        $labels = [];
        $data = [];

        $dateFormat = $this->languageService->sL(
            Constants::LANGUAGE_PATH . ':tx_cart.format.date'
        );

        for ($daysBefore = $days; $daysBefore >= 0; $daysBefore--) {
            $timeForLabel = strtotime('-' . $daysBefore . ' day');
            $startPeriod = strtotime('-' . $daysBefore . ' day 0:00:00');
            $endPeriod =  strtotime('-' . $daysBefore . ' day 23:59:59');

            if ($timeForLabel === false || $startPeriod === false || $endPeriod === false) {
                continue;
            }

            $labels[] = date($dateFormat, $timeForLabel);
            $data[] = $this->getOrderItemsInPeriod($startPeriod, $endPeriod);
        }

        return [
            $labels,
            $data,
        ];
    }

    public function getOrderItemsInPeriod(int $start, int $end): float
    {
        $constraints = [
            $this->queryBuilder->expr()->gte($this->options['fieldName'], $start),
            $this->queryBuilder->expr()->lte($this->options['fieldName'], $end),
        ];

        $this->queryBuilder
            ->selectLiteral(
                $this->queryBuilder->expr()->sum('tx_cart_domain_model_order_item.' . $this->options['sum'], 'turnover')
            )
            ->from('tx_cart_domain_model_order_item');

        if ($constraints !== []) {
            $this->queryBuilder->where(... $constraints);
        }

        return $this->queryBuilder->executeQuery()->fetchOne() ?? 0;
    }
}
