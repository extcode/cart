<?php

namespace Extcode\Cart\Domain\Finisher\Cart;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;
use TYPO3\CMS\Extbase\Mvc\Web\Request;

/**
 * CheckAvailability Hook Interface
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
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
     * @return bool
     */
    public function getProductFromRequest(
        Request $request,
        Cart $cart
    );
}
