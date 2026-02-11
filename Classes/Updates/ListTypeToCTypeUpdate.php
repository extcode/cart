<?php

declare(strict_types=1);

namespace Extcode\Cart\Updates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Upgrades\AbstractListTypeToCTypeUpdate;

#[UpgradeWizard('cart_updateListTypeToCType')]
class ListTypeToCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'cart_cart' => 'cart_cart',
            'cart_currency' => 'cart_currency',
            'cart_minicart' => 'cart_minicart',
            'cart_order' => 'cart_order',
        ];
    }

    public function getTitle(): string
    {
        return 'Update cart list_type to CType.';
    }

    public function getDescription(): string
    {
        return 'Update all cart list_type plugin to CType.';
    }
}
