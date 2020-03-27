<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class PaymentUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

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
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

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
        $provider = $payment->getAdditional('payment_service');

        $data = [
            'orderItem' => $orderItem,
            'cart' => $cart,
            'provider' => $provider,
            'providerUsed' => false,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
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

        $signalSlotDispatcher = $this->objectManager->get(
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

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            [$data]
        );
    }
}
