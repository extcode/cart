<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Constants;
use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetApi;
use TYPO3\CMS\Dashboard\Widgets\ChartDataProviderInterface;

/**
 * Base for the chart providers with one value per day.
 *
 * Options:
 * - filter, storagePids: see OrderItemCriteria::fromOptions()
 * - fieldName: date field of the order item, defaults to order_date
 * - days: number of days before today, defaults to 28
 *
 * The orders of the whole period are summed up per day in the time zone of
 * the server.
 */
abstract class AbstractPerDayProvider implements ChartDataProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    protected readonly array $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly OrderItemQuery $orderItemQuery,
        private readonly StoragePidAccess $storagePidAccess,
        array $options = [],
    ) {
        $this->options = array_merge(
            [
                'fieldName' => 'order_date',
                'days' => 28,
            ],
            $this->getDefaultOptions(),
            $options
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getChartData(): array
    {
        $days = $this->getDays();
        $start = (int) strtotime('-' . $days . ' day 0:00:00');
        $end = (int) strtotime('today 23:59:59');

        $dateFormat = self::getDateFormat();
        $labels = [];
        $data = [];
        for ($daysBefore = $days; $daysBefore >= 0; $daysBefore--) {
            $day = (int) strtotime('-' . $daysBefore . ' day 12:00:00');
            $labels[] = date($dateFormat, $day);
            $data[date('Y-m-d', $day)] = $this->castValue(0);
        }

        foreach ($this->getValuesPerDay($start, $end) as $day => $value) {
            if (array_key_exists($day, $data)) {
                $data[$day] = $value;
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'backgroundColor' => WidgetApi::getDefaultChartColors()[0],
                    'border' => 0,
                    'data' => array_values($data),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [];
    }

    /**
     * @return null|string field of the order item to sum up, null to count the order items
     */
    abstract protected function getValueField(): ?string;

    abstract protected function castValue(mixed $value): float|int;

    /**
     * @return array<string, float|int> sum of the values per day (Y-m-d)
     */
    protected function getValuesPerDay(int $start, int $end): array
    {
        $criteria = $this->storagePidAccess->restrict(OrderItemCriteria::fromOptions($this->options));

        $valuesPerDay = [];
        foreach ($this->orderItemQuery->findValuesInPeriod($criteria, $this->getFieldName(), $this->getValueField(), $start, $end) as $row) {
            $day = date('Y-m-d', $row['date']);
            $valuesPerDay[$day] = ($valuesPerDay[$day] ?? $this->castValue(0)) + $this->castValue($row['value']);
        }

        return $valuesPerDay;
    }

    private function getFieldName(): string
    {
        $fieldName = $this->options['fieldName'];

        return is_string($fieldName) && $fieldName !== '' ? $fieldName : 'order_date';
    }

    private function getDays(): int
    {
        $days = $this->options['days'];

        return is_numeric($days) ? max(0, (int) $days) : 28;
    }

    private static function getDateFormat(): string
    {
        $backendUser = $GLOBALS['BE_USER'] ?? null;
        if (!$backendUser instanceof BackendUserAuthentication) {
            return 'Y-m-d';
        }

        $dateFormat = GeneralUtility::makeInstance(LanguageServiceFactory::class)
            ->createFromUserPreferences($backendUser)
            ->sL(Constants::LANGUAGE_PATH . ':tx_cart.format.date')
        ;

        return $dateFormat !== '' ? $dateFormat : 'Y-m-d';
    }
}
