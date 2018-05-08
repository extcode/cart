<?php

namespace Extcode\Cart\Hooks;

/**
 * CheckAvailability Hook Interface
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
interface CartProductHookInterface
{
    /**
     * @param array $params
     * @return bool
     */
    public function checkAvailability(array $params);

    /**
     * @param array $requestArguments
     * @param array $taxClasses
     *
     * @return bool
     */
    public function getProductFromRequest(array $requestArguments, array $taxClasses);
}
