<?php

namespace Extcode\Cart\Domain\Model\Order;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Order AbstractService Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
abstract class AbstractService extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Order Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item = null;

    /**
     * Service Country
     *
     * @var string
     * @validate NotEmpty
     */
    protected $serviceCountry;

    /**
     * Service Id
     *
     * @var int
     * @validate NotEmpty
     */
    protected $serviceId;

    /**
     * Name
     *
     * @var string
     * @validate NotEmpty
     */
    protected $name = '';

    /**
     * Status
     *
     * @var string
     * @validate NotEmpty
     */
    protected $status = 'open';

    /**
     * Net
     *
     * @var float
     * @validate NotEmpty
     */
    protected $net = 0.0;

    /**
     * Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $gross = 0.0;

    /**
     * Order Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     * @validate NotEmpty
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     * @validate NotEmpty
     */
    protected $tax = 0.0;

    /**
     * Note
     *
     * @var string
     */
    protected $note = '';

    /**
     * Returns ShippingArray
     *
     * @return array
     */
    public function toArray()
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
     * @return \Extcode\Cart\Domain\Model\Order\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getServiceCountry()
    {
        return $this->serviceCountry;
    }

    /**
     * @param string $serviceCountry
     */
    public function setServiceCountry($serviceCountry)
    {
        $this->serviceCountry = $serviceCountry;
    }

    /**
     * @return int
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param int $serviceId
     */
    public function setServiceId($serviceId)
    {
        $this->serviceId = $serviceId;
    }

    /**
     * Returns Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets Name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets Status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Returns Gross
     *
     * @return float
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * Sets Gross
     *
     * @param float $gross
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    /**
     * Returns Net
     *
     * @return float
     */
    public function getNet()
    {
        return $this->net;
    }

    /**
     * Sets Net
     *
     * @param float $net
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * Gets Tax Class
     *
     * @return \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Sets Tax Class
     *
     * @param \Extcode\Cart\Domain\Model\Order\TaxClass $taxClass
     */
    public function setTaxClass(\Extcode\Cart\Domain\Model\Order\TaxClass $taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * Gets Tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Sets Tax
     *
     * @param float $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
    }

    /**
     * Sets Note
     *
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Gets Note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}
