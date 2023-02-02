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

abstract class AbstractService extends AbstractEntity
{
    protected ?Item $item = null;

    protected string $serviceCountry = '';

    /**
     * @Validate("NotEmpty")
     */
    protected ?int $serviceId = null;

    /**
     * @Validate("NotEmpty")
     */
    protected string $name = '';

    /**
     * @Validate("NotEmpty")
     */
    protected string $status = 'open';

    protected float $net = 0.0;

    protected float $gross = 0.0;

    protected ?TaxClass $taxClass = null;

    /**
     * @Validate("NotEmpty")
     */
    protected float $tax = 0.0;

    protected string $note = '';

    public function toArray(): array
    {
        $service = [
            'service_country' => $this->getServiceCountry(),
            'service_id' => $this->getServiceId(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'net' => $this->getNet(),
            'gross' => $this->getGross(),
            'tax' => $this->getTax(),
        ];

        if ($this->getTaxClass()) {
            $service['taxClass'] = $this->getTaxClass()->toArray();
        } else {
            $service['taxClass'] = null;
        }

        $service['note'] = $this->getNote();

        return $service;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    public function getServiceCountry(): string
    {
        return $this->serviceCountry;
    }

    public function setServiceCountry(string $serviceCountry): void
    {
        $this->serviceCountry = $serviceCountry;
    }

    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    public function setServiceId(int $serviceId): void
    {
        $this->serviceId = $serviceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function setGross(float $gross): void
    {
        $this->gross = $gross;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    public function setNet(float $net): void
    {
        $this->net = $net;
    }

    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    public function setTaxClass(TaxClass $taxClass): void
    {
        $this->taxClass = $taxClass;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): void
    {
        $this->tax = $tax;
    }

    public function setNote(string $note): void
    {
        $this->note = $note;
    }

    public function getNote(): string
    {
        return $this->note;
    }
}
