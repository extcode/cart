<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log\Model;

use TYPO3\CMS\Core\Log\LogLevel;

final readonly class Log implements LogInterface
{
    public function __construct(
        private string $logLevel,
        private string $identifier,
        private string $message,
        private array $arguments = [],
    ) {}

    public static function info(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::INFO,
            $identifier,
            $message,
            $arguments,
        );
    }

    public static function notice(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::NOTICE,
            $identifier,
            $message,
            $arguments,
        );
    }

    public static function warning(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::WARNING,
            $identifier,
            $message,
            $arguments,
        );
    }

    public static function error(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self {
        return new self(
            LogLevel::ERROR,
            $identifier,
            $message,
            $arguments,
        );
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
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
