<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class PaymentUtility
{
    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Order Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $orderItem;

    /**
     * Handle Payment
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     *
     * @return bool
     */
    public function handlePayment(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $this->beforeHandlePayment($orderItem, $cart);

        $payment = $cart->getPayment();
        if (method_exists($payment, 'getProvider')) {
            $provider = $payment->getProvider();
        }

        $data = [
            'orderItem' => $orderItem,
            'cart' => $cart,
            'provider' => $provider,
            'providerUsed' => false,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $params = $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );

        $this->afterHandlePayment($orderItem, $cart);

        return $params[0]['providerUsed'];
    }

    /**
     * Before Handle Payment
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function beforeHandlePayment(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'orderItem' => $orderItem,
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }

    /**
     * After Handle Payment
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function afterHandlePayment(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Cart\Cart $cart
    ) {
        $data = [
            'orderItem' => $orderItem,
            'cart' => $cart,
        ];

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }
}
