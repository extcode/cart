<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Backend\ActionController;
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\Domain\Repository\Order\ShippingRepository;
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ShippingController extends ActionController
{
    public function __construct(
        private readonly ShippingRepository $shippingRepository,
        private readonly LogServiceInterface $logService,
    ) {
    }

    public function updateAction(Shipping $shipping): ResponseInterface
    {
        $this->shippingRepository->update($shipping);
        $this->logService->write(
            Log::info(
                self::getOrderItemUid($shipping),
                'updateShipping',
                'Shipping was set to ' . $shipping->getStatus() . '.',
                [
                    'time' => time(),
                ]
            )
        );

        $event = new UpdateServiceEvent($shipping);
        $this->eventDispatcher->dispatch($event);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_shipping_action.success',
            'Cart'
        );

        $this->addFlashMessage($msg);

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $shipping->getItem()]);
    }

    private static function getOrderItemUid(Shipping $shipping): int
    {
        $orderItemUid = $shipping->getItem()?->getUid();

        if (is_null($orderItemUid)) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }

        return $orderItemUid;
    }
}
