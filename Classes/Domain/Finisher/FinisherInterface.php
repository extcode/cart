<?php
declare(strict_types = 1);

namespace Extcode\Cart\Domain\Finisher;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface FinisherInterface
{
    /**
     * Executes the finisher
     *
     * @param FinisherContext $finisherContext The Finisher context that contains the current Form Runtime and Response
     * @return string|null
     */
    public function execute(FinisherContext $finisherContext);
}
