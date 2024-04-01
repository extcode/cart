<?php

namespace Extcode\Cart\Event\Form;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;

class AddToCartFinisherEvent
{
    private array $errors = [];

    /**
     * @var Product[]
     */
    private array $cartProducts = [];

    public function __construct(
        private array $formValues,
        private Cart $cart
    ) {}

    public function getFormValues(): array
    {
        return $this->formValues;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * @return Product[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    public function getCartProducts(): array
    {
        return $this->cartProducts;
    }

    public function setCartProducts(array $cartProducts): void
    {
        $this->cartProducts = $cartProducts;
    }
}
