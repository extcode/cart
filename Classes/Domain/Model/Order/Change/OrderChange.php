<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order\Change;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;

/**
 * A change of an order, e.g. of the payment status, stored in
 * tx_cart_domain_model_order_change.
 */
final readonly class OrderChange
{
    public function __construct(
        /**
         * The uid of the order item.
         */
        public int $orderItem,
        /**
         * E.g. OrderChangeType::PAYMENT_STATUS.
         */
        public string $type,
        public string $oldValue,
        public string $newValue,
        public DateTimeImmutable $date,
        /**
         * E.g. OrderChangeSource::BACKEND.
         */
        public string $source,
        /**
         * The uid of the backend user, 0 if there is none, e.g. on the CLI.
         */
        public int $backendUser = 0,
        /**
         * What was changed if the type is not enough, e.g. "product:12".
         */
        public string $subject = '',
        /**
         * @var array<string, mixed> details of the change
         */
        public array $data = [],
        /**
         * The uid of the stored change, 0 if not stored yet.
         */
        public int $uid = 0,
    ) {
    }
}
