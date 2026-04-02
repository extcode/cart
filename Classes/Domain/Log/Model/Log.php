<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log\Model;

final readonly class Log implements LogInterface
{
    public function __construct(
        private LogLevel $logLevel,
        private int $orderItemId,
        private string $type,
        private string $message,
        private array $arguments = [],
    ) {}

    public static function info(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::INFO,
            $orderItemId,
            $type,
            $message,
            $arguments,
        );
    }

    public static function notice(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::NOTICE,
            $orderItemId,
            $type,
            $message,
            $arguments,
        );
    }

    public static function warning(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::WARNING,
            $orderItemId,
            $type,
            $message,
            $arguments,
        );
    }

    public static function error(
        int $orderItemId,
        string $type,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::ERROR,
            $orderItemId,
            $type,
            $message,
            $arguments,
        );
    }

    public function getLogLevel(): LogLevel
    {
        return $this->logLevel;
    }

    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

}
