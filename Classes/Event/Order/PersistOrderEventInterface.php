<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

interface PersistOrderEventInterface extends EventInterface
{
    public function setSettings(array $settings): void;

    public function getStoragePid(): int;

    public function getTaxClasses(): array;

    public function setTaxClasses(array $taxClasses): void;
}
