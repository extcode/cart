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
use TYPO3\CMS\Extbase\Mvc\Request;

final class RetrieveProductsFromRequestEvent implements RetrieveProductsFromRequestEventInterface
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var Product[]
     */
    private $products = [];

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request, Cart $cart)
    {
        $this->cart = $cart;
        $this->request = $request;
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

    public function addError(array $error): void
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
}
