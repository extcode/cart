<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log\Model;

interface LogInterface
{
    public function __construct(
        LogLevel $logLevel,
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    );

    public static function info(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self;

    public static function notice(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self;

    public static function warning(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self;

    public static function error(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self;

    public function getLogLevel(): LogLevel;

    public function getOrderItemId(): int;

    public function getType(): string;

    public function getMessage(): string;

    public function getArguments(): array;
}
