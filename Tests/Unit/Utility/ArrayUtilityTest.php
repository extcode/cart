<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Utility\ArrayUtility;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ArrayUtility::class)]
final class ArrayUtilityTest extends UnitTestCase
{
    private const ARRAY = [
        'mail' => [
            'buyer' => [
                'fromName' => 'Shop',
                'attachments' => ['1' => 'EXT:sitepackage/Resources/Public/Files/AGB.pdf'],
                'empty' => [],
            ],
        ],
    ];

    #[Test]
    public function getValueByPathWithFallbackReturnsTheValueOfThePath(): void
    {
        self::assertSame('Shop', ArrayUtility::getValueByPathWithFallback(self::ARRAY, 'mail/buyer/fromName'));
        self::assertSame([], ArrayUtility::getValueByPathWithFallback(self::ARRAY, 'mail/buyer/empty', '/', 'fallback'));
    }

    #[Test]
    public function getValueByPathWithFallbackReturnsTheFallbackForAMissingPath(): void
    {
        self::assertNull(ArrayUtility::getValueByPathWithFallback(self::ARRAY, 'mail/seller/fromName'));
        self::assertSame('fallback', ArrayUtility::getValueByPathWithFallback(self::ARRAY, 'mail/buyer/fromName/sub', '/', 'fallback'));
    }

    #[Test]
    public function getValueByPathWithFallbackTakesTheDelimiter(): void
    {
        self::assertSame('Shop', ArrayUtility::getValueByPathWithFallback(self::ARRAY, 'mail.buyer.fromName', '.'));
    }

    #[Test]
    public function getArrayByPathWithFallbackReturnsTheArrayOfThePath(): void
    {
        self::assertSame(['1' => 'EXT:sitepackage/Resources/Public/Files/AGB.pdf'], ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail/buyer/attachments'));
        self::assertSame([], ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail.buyer.empty', '.'), 'An empty array is not missing.');
    }

    #[Test]
    public function getArrayByPathWithFallbackReturnsNullForAMissingPathOrNoArray(): void
    {
        self::assertNull(ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail/seller/attachments'));
        self::assertNull(ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail/buyer/fromName'));
    }

    #[Test]
    public function getArrayByPathWithFallbackReturnsTheFallbackForAMissingPathOrNoArray(): void
    {
        self::assertSame([], ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail/seller/attachments', '/', []));
        self::assertSame(['fallback'], ArrayUtility::getArrayByPathWithFallback(self::ARRAY, 'mail/buyer/fromName', '/', ['fallback']));
    }
}
