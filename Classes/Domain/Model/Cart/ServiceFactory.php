<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class ServiceFactory
{
    public function getService(int $serviceKey, array $serviceConfig, bool $preset = false): ServiceInterface
    {
        if (!empty($serviceConfig['className'])) {
            $className = $serviceConfig['className'];
        } else {
            $className = Service::class;
        }

        $service = new $className(
            $serviceKey,
            $serviceConfig
        );

        if (!$service instanceof ServiceInterface) {
            throw new \UnexpectedValueException($className . ' must implement interface ' . ServiceInterface::class, 123);
        }

        if ($preset) {
            $service->setPreset(true);
        }

        return $service;
    }
}
