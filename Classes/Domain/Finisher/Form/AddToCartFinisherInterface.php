<?php

namespace Extcode\Cart\Domain\Finisher\Form;

use Extcode\Cart\Domain\Model\Cart\Cart;

/**
 * CheckAvailability Hook Interface
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
interface AddToCartFinisherInterface
{
    /**
     * @param array $formValues
     * @param Cart $cart
     */
    public function getProductFromForm(
        array $formValues,
        Cart $cart
    );
}
