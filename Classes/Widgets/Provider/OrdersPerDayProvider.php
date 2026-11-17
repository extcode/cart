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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

class OrdersPerDayProvider implements ChartDataProviderInterface
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

    public function getOrderItemsInPeriod(int $start, int $end): int
    {
        $queryBuilder = clone $this->queryBuilder;

        $constraints = [
            $queryBuilder->expr()->gte($this->options['fieldName'], $start),
            $queryBuilder->expr()->lte($this->options['fieldName'], $end),
        ];

        $queryBuilder
            ->count('*')
            ->from('tx_cart_domain_model_order_item');

        $filter = $this->options['filter'] ?? [];
        foreach (['payment', 'shipping'] as $type) {
            $status = is_array($filter) && is_array($filter[$type] ?? null) ? ($filter[$type]['status'] ?? '') : '';
            if (!is_string($status) || $status === '') {
                continue;
            }

            $queryBuilder->join(
                'tx_cart_domain_model_order_item',
                'tx_cart_domain_model_order_' . $type,
                $type,
                $queryBuilder->expr()->eq(
                    'tx_cart_domain_model_order_item.uid',
                    $queryBuilder->quoteIdentifier($type . '.item')
                )
            );

            $constraints[] = $queryBuilder->expr()->eq(
                $type . '.status',
                $queryBuilder->createNamedParameter($status, Connection::PARAM_STR)
            );
        }

        $queryBuilder->where(...$constraints);

        $count = $queryBuilder->executeQuery()->fetchOne();

        return is_numeric($count) ? (int)$count : 0;
    }
}
