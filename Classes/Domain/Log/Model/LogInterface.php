<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log\Model;

interface LogInterface
{
    public function __construct(
        string $logLevel,
        string $identifier,
        string $message,
        array $arguments = [],
    );

    public static function info(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self;

    public static function notice(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self;

    public static function warning(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self;

    public static function error(
        string $identifier,
        string $message,
        array $arguments = [],
    ): self;

    public function getLogLevel(): string;

    public function getIdentifier(): string;

    public function getMessage(): string;

    public function getArguments(): array;
}
