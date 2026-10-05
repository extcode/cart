<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log;

use Extcode\Cart\Domain\Log\Model\LogInterface;
use Psr\Log\LoggerInterface;

final readonly class LogService implements LogServiceInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function write(
        LogInterface $log
    ): void {
        $this->logger->log(
            $log->getLogLevel()->value,
            $log->getMessage(),
            [
                'log' => $log,
            ]
        );
    }
}
