<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order\Change;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Http\ApplicationType;

/**
 * The TYPO3 entry point a change of an order comes through. It is determined
 * from the request, who or what triggered the change is the initiator of the
 * change, e.g. a payment provider.
 */
enum OrderChangeSource: string
{
    /**
     * A request in the backend, e.g. an editor in the order details.
     */
    case Backend = 'backend';

    /**
     * A request in the frontend, e.g. the notification of a payment provider.
     */
    case Frontend = 'frontend';

    /**
     * A command or a scheduler task.
     */
    case Cli = 'cli';

    public static function fromRequest(?ServerRequestInterface $request): self
    {
        if ($request !== null && is_int($request->getAttribute('applicationType'))) {
            return ApplicationType::fromRequest($request)->isFrontend() ? self::Frontend : self::Backend;
        }

        return Environment::isCli() ? self::Cli : self::Frontend;
    }
}
