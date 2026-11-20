<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use InvalidArgumentException;

/**
 * An email configuration in TypoScript is invalid, the message is the reason.
 */
final class InvalidMailConfigurationException extends InvalidArgumentException
{
}
