<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Status;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * A payment or shipping status registered in Configuration/OrderStatus.php.
 */
final readonly class Status
{
    public function __construct(
        public ServiceType $serviceType,
        public string $identifier,
        /**
         * A LLL reference or a plain text.
         */
        public string $label,
    ) {
    }

    public function getLocalizedLabel(): string
    {
        if (!str_starts_with($this->label, 'LLL:')) {
            return $this->label;
        }

        return LocalizationUtility::translate($this->label) ?? $this->label;
    }
}
