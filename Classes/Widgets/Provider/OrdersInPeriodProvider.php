<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Dashboard\Widgets\NumberWithIconDataProviderInterface;

/**
 * Number of orders in a period, see AbstractPeriodProvider for the options.
 */
class OrdersInPeriodProvider extends AbstractPeriodProvider implements NumberWithIconDataProviderInterface
{
    public function getNumber(): int
    {
        $period = $this->getPeriod();

        return $this->orderItemQuery->countInPeriod(
            $this->getCriteria(),
            $this->getFieldName(),
            $period->start,
            $period->end
        );
    }
}
