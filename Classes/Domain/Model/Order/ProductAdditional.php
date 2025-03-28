<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class ProductAdditional extends AbstractEntity
{
    /**
     * @var string don't remove, it's necessary for extbase database mapping
     */
    protected string $additional;

    public function __construct(
        protected string $additionalType,
        protected string $additionalKey,
        protected string $additionalValue,
        protected string $additionalData = ''
    ) {}

    public function getAdditionalType(): string
    {
        return $this->additionalType;
    }

    public function getAdditionalKey(): string
    {
        return $this->additionalKey;
    }

    public function getAdditionalValue(): string
    {
        return $this->additionalValue;
    }

    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    public function setAdditionalData(string $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    public function setAdditional(array $additional): void
    {
        $this->additional = json_encode($additional);
    }
}
