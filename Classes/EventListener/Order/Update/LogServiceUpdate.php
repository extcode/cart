<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Update;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\UpdateServiceEvent;
use Psr\Log\LoggerInterface;

class LogServiceUpdate
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(UpdateServiceEvent $event): void
    {
        $service = $event->getService();

        $cleanProperties = $service->_getCleanProperties();

        $this->logger->debug(
            'Log Service Update',
            [
                'className' => get_class($service),
                'old state' => $cleanProperties['status'],
                'new stage' => $service->getStatus(),
            ]
        );
    }
}
