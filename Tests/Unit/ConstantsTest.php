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
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConstantsTest extends UnitTestCase
{
    /**
     * @test
     */
    public function constantForDoktypeOfCartIsSetCorrectly()
    {
        self::assertEquals(181, Constants::DOKTYPE_CART_CART);
    }
}
