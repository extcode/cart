<?php

declare(strict_types=1);

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Site\Entity\Site;
use TypeError;

final readonly class Typo3GlobalsUtility
{
    public static function getTypo3BackendUser(): BackendUserAuthentication
    {
        $user = $GLOBALS['BE_USER'] ?? null;

        if (($user instanceof BackendUserAuthentication) === false) {
            throw new TypeError('$GLOBALS[\'BE_USER\'] was not of type BackendUserAuthentication, got: ' . $user, 1769004660);
        }

        return $user;
    }

    public static function getTypo3Request(): ServerRequestInterface
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if (($request instanceof ServerRequestInterface) === false) {
            throw new TypeError('$GLOBALS[\'TYPO3_REQUEST\'] was not of type ServerRequestInterface, got: ' . $request, 1776420280);
        }

        return $request;
    }

    public static function getSiteFromTypo3Request(): Site
    {
        $request = self::getTypo3Request();

        $site = $request->getAttribute('site');

        if (($site instanceof Site) === false) {
            throw new TypeError('$site was not of type Site, got: ' . $site, 1776450259);
        }

        return $site;
    }
}
