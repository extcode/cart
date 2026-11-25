<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\Status\StatusChangeOutcome;
use Extcode\Cart\Domain\Order\Status\StatusChangeResult;
use Extcode\Cart\Domain\Order\Status\StatusChangeService;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Psr\Http\Message\ServerRequestInterface;

/**
 * The derived status is changed via the StatusChangeService, so the order
 * history, the events and the status mails work like for a manual change.
 */
final readonly class OrderShippingStatusUpdater
{
    public function __construct(
        private ItemRepository $itemRepository,
        private OrderProductQuantityCalculator $orderProductQuantityCalculator,
        private StatusChangeService $statusChangeService,
    ) {
    }

    public function changeToDerivedStatus(
        int $orderItemUid,
        string $initiator = '',
        ?ServerRequestInterface $request = null,
        ?OrderChangeSource $source = null
    ): ?StatusChangeResult {
        $orderItem = $this->itemRepository->findByUid($orderItemUid);
        $shipping = $orderItem instanceof Item ? $orderItem->getShipping() : null;
        if ($shipping === null) {
            return null;
        }

        $orderProductQuantities = $this->orderProductQuantityCalculator->calculateForOrderItem($orderItemUid);
        $storedStatus = $shipping->getStatus();
        $derivedStatus = ShippingStatusDerivation::derive($storedStatus, $orderProductQuantities);
        if ($derivedStatus === $storedStatus) {
            return null;
        }

        $result = $this->statusChangeService->change($shipping, $derivedStatus, $storedStatus, $initiator, $request, $source);
        if ($result->outcome !== StatusChangeOutcome::Stale || $result->storedStatus === null) {
            return $result;
        }

        // the loaded order is outdated, e.g. the status was changed in the same request before
        $derivedStatus = ShippingStatusDerivation::derive($result->storedStatus, $orderProductQuantities);
        if ($derivedStatus === $result->storedStatus) {
            return null;
        }

        return $this->statusChangeService->change($shipping, $derivedStatus, $result->storedStatus, $initiator, $request, $source);
    }
}
