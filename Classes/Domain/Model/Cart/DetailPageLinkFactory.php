<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class DetailPageLinkFactory implements DetailPageLinkFactoryInterface
{
    public function getDetailPageLink(
        int $detailPageUid,
        string $extensionName = '',
        string $pluginName = '',
        string $controller = '',
        string $action = '',
    ): ?DetailPageLink {
        if ($detailPageUid > 0) {
            return new DetailPageLink($detailPageUid, $extensionName, $pluginName, $controller, $action);
        }
        return null;
    }
}
