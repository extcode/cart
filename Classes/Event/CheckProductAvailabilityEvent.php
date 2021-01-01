<?php
declare(strict_types=1);
namespace Extcode\Cart\Event;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class CheckProductAvailabilityEvent implements CheckProductAvailabilityEventInterface
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var mixed
     */
    private $quantity;

    /**
     * @var string
     */
    private $mode;

    /**
     * @var bool
     */
    protected $available = true;

    /**
     * @var FlashMessage[]
     */
    protected $messages = [];

    public function __construct(Cart $cart, Product $product, $quantity, string $mode = 'update')
    {
        $this->cart = $cart;
        $this->product = $product;
        $this->quantity = $quantity;
        $this->mode = $mode;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param FlashMessage[] $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @param FlashMessage $message
     */
    public function addMessage(FlashMessage $message): void
    {
        $this->messages[] = $message;
    }
}
