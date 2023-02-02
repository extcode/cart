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
use TYPO3\CMS\Extbase\Mvc\Request;

final class RetrieveProductsFromRequestEvent implements RetrieveProductsFromRequestEventInterface
{
    private bool $isPropagationStopped = false;

    /**
     * @var FlashMessage[]
     */
    private array $errors = [];

    /**
     * @var Product[]
     */
    private array $products = [];

    public function __construct(
        private readonly Request $request,
        private readonly Cart $cart
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function addError(FlashMessage $error): void
    {
        $this->errors[] = $error;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
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
