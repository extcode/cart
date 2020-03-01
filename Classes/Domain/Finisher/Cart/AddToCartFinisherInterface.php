<?php

namespace Extcode\Cart\Domain\Finisher\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;
use TYPO3\CMS\Extbase\Mvc\Request;

interface AddToCartFinisherInterface
{
    /**
     * @param Request $request
     * @param Product $cartProduct,
     * @param Cart $cart
     *
     * @return \Extcode\Cart\Domain\Model\Dto\AvailabilityResponse
     */
    public function checkAvailability(
        Request $request,
        Product $cartProduct,
        Cart $cart
    ) : \Extcode\Cart\Domain\Model\Dto\AvailabilityResponse;

    /**
     * @param Request $request
     * @param Cart $cart
     *
     * @return array
     */
    public function getProductFromRequest(
        Request $request,
        Cart $cart
    ): array;
}
