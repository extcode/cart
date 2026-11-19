<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderHistory;

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
 * The channel a change of an order comes through. Who or what triggered the
 * change is the initiator of the change, e.g. a payment provider.
 */
enum OrderChangeSource: string
{
    /**
     * A request in the backend, e.g. an editor in the order details.
     */
    case Backend = 'backend';

    /**
     * A request of a browser in the frontend, e.g. the customer redirected back
     * from a payment provider.
     */
    case Frontend = 'frontend';

    /**
     * A server-to-server request, e.g. the notification (IPN, webhook) of a
     * payment provider or an ERP. TYPO3 handles it as a frontend request, so it
     * is never determined from the request, the code handling it sets it.
     */
    case Api = 'api';

    /**
     * A command or a scheduler task.
     */
    case Cli = 'cli';

    /**
     * Backend, Frontend or Cli, Api can not be determined from a request.
     */
    public static function fromRequest(?ServerRequestInterface $request): self
    {
        if ($request !== null && is_int($request->getAttribute('applicationType'))) {
            return ApplicationType::fromRequest($request)->isFrontend() ? self::Frontend : self::Backend;
        }

        return Environment::isCli() ? self::Cli : self::Frontend;
    }
}
