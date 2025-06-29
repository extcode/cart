<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface DetailPageLinkFactoryInterface
{
    public function getDetailPageLink(
        int $detailPageUid,
        string $extensionName = '',
        string $pluginName = '',
        string $controller = '',
        string $action = ''
    ): ?DetailPageLink;
}
