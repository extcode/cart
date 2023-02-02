<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class TaxClass extends AbstractEntity
{
    /**
     * @Validate("NotEmpty")
     */
    protected string $title = '';

    /**
     * @Validate("NotEmpty")
     */
    protected string $value = '';

    /**
     * @Validate("NotEmpty")
     */
    protected float $calc = 0.0;

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'value' => $this->getValue(),
            'calc' => $this->getCalc(),
        ];
    }

    public function getCalc(): float
    {
        return $this->calc;
    }

    public function setCalc(float $calc): void
    {
        $this->calc = $calc;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
