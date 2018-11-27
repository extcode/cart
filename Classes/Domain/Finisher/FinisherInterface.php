<?php
declare(strict_types = 1);

namespace Extcode\Cart\Domain\Finisher;

/**
 * Finisher that can be attached to a form in order to be invoked
 * as soon as the complete form is submitted
 *
 * Scope: frontend
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
