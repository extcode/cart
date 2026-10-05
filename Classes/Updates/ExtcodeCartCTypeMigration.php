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

#[UpgradeWizard('extcodeCartCTypeMigration')]
final class ExtcodeCartCTypeMigration extends AbstractListTypeToCTypeUpdate
{
    public function getTitle(): string
    {
        return 'Migrate "Extcode Cart" plugins to content elements.';
    }

    public function getDescription(): string
    {
        return 'The "Extcode Cart" plugins are now registered as content element. Update migrates existing records and backend user permissions.';
    }

    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'cart' => 'cart_cart',
        ];
    }
}
