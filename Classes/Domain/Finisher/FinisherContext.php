<?php

namespace Extcode\Cart\Domain\Finisher;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;

class FinisherContext
{
    /**
     * If TRUE further finishers won't be invoked
     *
     * @var bool
     */
    protected $cancelled = false;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $orderItem;

    /**
     * The assigned controller context which might be needed by the finisher.
     *
     * @var \TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext
     */
    protected $controllerContext;

    /**
     * @param Cart $cart
     * @param Order\Item $orderItem
     * @param ControllerContext $controllerContext
     * @internal
     */
    public function __construct(
        $settings,
        Cart $cart,
        Order\Item $orderItem,
        ControllerContext $controllerContext
    ) {
        $this->settings = $settings;
        $this->cart = $cart;
        $this->orderItem = $orderItem;
        $this->controllerContext = $controllerContext;
    }

    /**
     * Cancels the finisher invocation after the current finisher
     *
     * @api
     */
    public function cancel()
    {
        $this->cancelled = true;
    }

    /**
     * TRUE if no further finishers should be invoked. Defaults to FALSE
     *
     * @return bool
     * @internal
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return Order\Item
     * @api
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param Order\Item $orderItem
     * @api
     */
    public function setOrderItem(Order\Item $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * The Cart that is associated with the current finisher
     *
     * @return Cart
     * @api
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @return ControllerContext
     * @api
     */
    public function getControllerContext()
    {
        return $this->controllerContext;
    }
}
