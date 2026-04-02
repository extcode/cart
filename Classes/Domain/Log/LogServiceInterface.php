<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log;

use Extcode\Cart\Domain\Log\Model\LogInterface;

interface LogServiceInterface
{
    public function write(LogInterface $log): void;
}
