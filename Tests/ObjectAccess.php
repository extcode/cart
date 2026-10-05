<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use ReflectionProperty;

class ObjectAccess
{
    public static function setProperty(object $instance, string $propertyName, mixed $value): void
    {
        $reflection = new ReflectionProperty($instance::class, $propertyName);
        $reflection->setValue($instance, $value);
    }

    public static function getProperty(object $instance, string $propertyName): mixed
    {
        $reflection = new ReflectionProperty($instance::class, $propertyName);
        return $reflection->getValue($instance);
    }
}
