<?php

declare(strict_types=1);

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace Extcode\Cart\Tests\Unit;

use Extcode\Cart\Constants;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Constants::class)]
class ConstantsTest extends UnitTestCase
{
    #[Test]
    public function constantForDoktypeOfCartIsSetCorrectly(): void
    {
        self::assertEquals(181, Constants::DOKTYPE_CART_CART);
    }
}
