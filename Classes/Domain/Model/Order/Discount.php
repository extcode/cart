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

    /**
     * @Validate("NotEmpty")
     */
    protected string $title = '';

    /**
     * @Validate("NotEmpty")
     */
    protected string $code = '';

    /**
     * @Validate("NotEmpty")
     */
    protected float $gross = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected float $net = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected TaxClass $taxClass;

    /**
     * @Validate("NotEmpty")
     */
    protected float $tax = 0.0;

    public function __construct(
        string $title,
        string $code,
        float $gross,
        float $net,
        TaxClass $taxClass,
        float $tax
    ) {
        $this->title = $title;
        $this->code = $code;
        $this->gross = $gross;
        $this->net = $net;
        $this->taxClass = $taxClass;
        $this->tax = $tax;
    }

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
