<?php

declare(strict_types=1);

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ArrayUtility as CoreArrayUtility;

/**
 * Reads values of nested arrays by a path, e.g. of TypoScript or site settings,
 * with isValidPath() and getValueByPath() of the ArrayUtility of TYPO3 in one call.
 */
final readonly class ArrayUtility
{
    /**
     * @param array<array-key, mixed> $array
     * @param array<array-key, string>|string $path e.g. "mail/buyer/attachments" or ["mail", "buyer", "attachments"]
     * @param string $delimiter e.g. "." for the site settings
     * @param mixed $fallback returned if the path does not exist
     */
    public static function getValueByPathWithFallback(array $array, array|string $path, string $delimiter = '/', mixed $fallback = null): mixed
    {
        if (!CoreArrayUtility::isValidPath($array, $path, $delimiter)) {
            return $fallback;
        }

        return CoreArrayUtility::getValueByPath($array, $path, $delimiter);
    }

    /**
     * @param array<array-key, mixed> $array
     * @param array<array-key, string>|string $path e.g. "mail/buyer/attachments" or ["mail", "buyer", "attachments"]
     * @param string $delimiter e.g. "." for the site settings
     * @param null|array<array-key, mixed> $fallback returned if the path does not exist or is no array
     *
     * @return ($fallback is null ? null|array<array-key, mixed> : array<array-key, mixed>)
     */
    public static function getArrayByPathWithFallback(array $array, array|string $path, string $delimiter = '/', ?array $fallback = null): ?array
    {
        $value = self::getValueByPathWithFallback($array, $path, $delimiter);

        return is_array($value) ? $value : $fallback;
    }
}
