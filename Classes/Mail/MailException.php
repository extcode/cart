<?php

declare(strict_types=1);

namespace Extcode\Cart\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use RuntimeException;

/**
 * An email could not be sent, e.g. because the template is missing.
 */
final class MailException extends RuntimeException
{
}
