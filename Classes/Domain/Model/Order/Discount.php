<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Discount extends AbstractEntity
{
    protected Item $item;

    public function __construct(
        #[Validate(['validator' => 'NotEmpty'])]
        protected string $title,
        #[Validate(['validator' => 'NotEmpty'])]
        protected string $code,
        #[Validate(['validator' => 'NotEmpty'])]
        protected float $gross,
        #[Validate(['validator' => 'NotEmpty'])]
        protected float $net,
        #[Validate(['validator' => 'NotEmpty'])]
        protected TaxClass $taxClass,
        #[Validate(['validator' => 'NotEmpty'])]
        protected float $tax
    ) {}

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    public function getTax(): float
    {
        return $this->tax;
    }
}
