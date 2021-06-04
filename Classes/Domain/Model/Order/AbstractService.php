<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

abstract class AbstractService extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item = null;

    /**
     * @var string
     */
    protected $serviceCountry = '';

    /**
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $serviceId;

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $name = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $status = 'open';

    /**
     * @var float
     */
    protected $net = 0.0;

    /**
     * @var float
     */
    protected $gross = 0.0;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    protected $taxClass;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $tax = 0.0;

    /**
     * @var string
     */
    protected $note = '';

    /**
     * @return array
     */
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

    /**
     * Returns the Order Item
     *
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getServiceCountry(): string
    {
        return $this->serviceCountry;
    }

    /**
     * @param string $serviceCountry
     */
    public function setServiceCountry(string $serviceCountry)
    {
        $this->serviceCountry = $serviceCountry;
    }

    /**
     * @return int|null
     */
    public function getServiceId(): ?int
    {
        return $this->serviceId;
    }

    /**
     * @param int $serviceId
     */
    public function setServiceId(int $serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Sets Status
     *
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @param float $gross
     */
    public function setGross(float $gross)
    {
        $this->gross = $gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @param float $net
     */
    public function setNet(float $net)
    {
        $this->net = $net;
    }

    /**
     * @return TaxClass|null
     */
    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    /**
     * @param TaxClass $taxClass
     */
    public function setTaxClass(TaxClass $taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     */
    public function setTax(float $tax)
    {
        $this->tax = $tax;
    }

    /**
     * @param string $note
     */
    public function setNote(string $note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }
}
