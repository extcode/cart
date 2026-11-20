<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\OrderHistory\OrderChange;

/**
 * The result of StatusChangeService::change().
 */
final readonly class StatusChangeResult
{
    public function __construct(
        public StatusChangeOutcome $outcome,
        /**
         * The stored change if the status was changed.
         */
        public ?OrderChange $orderChange = null,
        /**
         * The reason of the veto if a listener vetoed the change.
         */
        public string $vetoReason = '',
        /**
         * @var list<string> errors of listeners of the AfterStatusChangeEvent, the status is changed anyway
         */
        public array $errors = [],
    ) {
    }

    public function isChanged(): bool
    {
        return $this->outcome === StatusChangeOutcome::Changed;
    }
}
