<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\ServiceInterface;

interface PaymentMethodsServiceInterface
{
    /**
     * @return ServiceInterface[]
     */
    public function getPaymentMethods(Cart $cart): array;
}
