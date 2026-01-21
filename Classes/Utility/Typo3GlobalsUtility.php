<?php

declare(strict_types=1);

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TypeError;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

final readonly class Typo3GlobalsUtility
{
    public static function getTypo3BackendUser(): BackendUserAuthentication
    {
        $user = $GLOBALS['BE_USER'] ?? null;

        if (!$user instanceof BackendUserAuthentication) {
            throw new TypeError('Globals BE_USER was not of type BackendUserAuthentication, got: ' . $user, 1769004660);
        }

        return $user;
    }
}
