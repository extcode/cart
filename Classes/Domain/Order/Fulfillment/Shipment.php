<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;

/**
 * No Extbase model: shipments are only written by services in database
 * transactions.
 */
final readonly class Shipment
{
    public function __construct(
        public int $orderItemUid,
        public string $status,
        /**
         * @var list<ShipmentProduct>
         */
        public array $products = [],
        public string $carrier = '',
        public string $trackingNumber = '',
        public string $trackingUrl = '',
        public ?DateTimeImmutable $shippedDate = null,
        public string $note = '',
        /**
         * A replacement may exceed the ordered count by the returned count.
         */
        public bool $isReplacement = false,
        public int $uid = 0,
    ) {
    }

    public function getOrderChangeSubject(): string
    {
        return 'shipment:' . $this->uid;
    }

    public function withStatus(string $status): self
    {
        return new self(
            $this->orderItemUid,
            $status,
            $this->products,
            $this->carrier,
            $this->trackingNumber,
            $this->trackingUrl,
            $this->shippedDate,
            $this->note,
            $this->isReplacement,
            $this->uid,
        );
    }
}
