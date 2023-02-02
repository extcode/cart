<?php

namespace Extcode\Cart\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

trait ServiceAttributeTrait
{
    protected float $serviceAttribute1 = 0.0;

    protected float $serviceAttribute2 = 0.0;

    protected float $serviceAttribute3 = 0.0;

    public function getServiceAttribute1(): float
    {
        return $this->serviceAttribute1;
    }

    public function setServiceAttribute1(float $serviceAttribute1): void
    {
        $this->serviceAttribute1 = $serviceAttribute1;
    }

    public function getServiceAttribute2(): float
    {
        return $this->serviceAttribute2;
    }

    public function setServiceAttribute2(float $serviceAttribute2): void
    {
        $this->serviceAttribute2 = $serviceAttribute2;
    }

    public function getServiceAttribute3(): float
    {
        return $this->serviceAttribute3;
    }

    public function setServiceAttribute3(float $serviceAttribute3): void
    {
        $this->serviceAttribute3 = $serviceAttribute3;
    }
}
