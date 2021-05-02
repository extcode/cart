<?php
declare(strict_types=1);
namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Psr\EventDispatcher\StoppableEventInterface;

final class FinishEvent implements EventInterface, StoppableEventInterface
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var OrderItem
     */
    private $orderItem;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var bool
     */
    private $isPropagationStopped = false;

    public function __construct(Cart $cart, OrderItem $orderItem, array $settings = [])
    {
        $this->cart = $cart;
        $this->orderItem = $orderItem;
        $this->settings = $settings;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setPropagationStopped(bool $isPropagationStopped): void
    {
        $this->isPropagationStopped = $isPropagationStopped;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}
