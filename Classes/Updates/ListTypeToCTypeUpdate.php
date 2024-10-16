<?php

namespace Extcode\Cart\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

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
