<?php

namespace Extcode\Cart\Domain\Model\Cart;

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
 * Cart Service Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
abstract class AbstractService
{

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * Id
     *
     * @var int
     */
    protected $id;

    /**
     * Fall Back ID
     *
     * @var int
     */
    protected $fallBackId;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Provider
     *
     * @var string
     */
    protected $provider;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * Status
     *
     * @var string
     */
    protected $status = 0;

    /**
     * Note
     *
     * @var string
     */
    protected $note;

    /**
     * ExtraType
     *
     * @var string
     */
    protected $extratype;

    /**
     * Extras
     *
     * @var Extra
     */
    protected $extras;

    /**
     * Free From
     *
     * @var float
     */
    protected $freeFrom;

    /**
     * Free Until
     *
     * @var float
     */
    protected $freeUntil;

    /**
     * Available From
     *
     * @var float
     */
    protected $availableFrom;

    /**
     * Available Until
     *
     * @var float
     */
    protected $availableUntil;

    /**
     * Is Net Price
     *
     * @var bool
     */
    protected $isNetPrice;

    /**
     * Is Preset
     *
     * @var bool
     */
    protected $isPreset;

    /**
     * Additional
     *
     * @var array Additional
     */
    protected $additional;

    /**
     * Gross
     *
     * @var float
     */
    protected $gross;

    /**
     * Net
     *
     * @var float
     */
    protected $net;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax;

    /**
     * __construct
     * @param $id
     * @param $name
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     * @param $status
     * @param $note
     * @param $isNetPrice
     */
    public function __construct(
        $id,
        $name,
        \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass,
        $status,
        $note,
        $isNetPrice
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->taxClass = $taxClass;
        $this->status = $status;
        $this->note = $note;
        $this->isNetPrice = $isNetPrice;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * Returns Provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Sets Provider
     *
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return int
     */
    public function getFallBackId()
    {
        return $this->fallBackId;
    }

    /**
     * @param int $fallBackId
     */
    public function setFallBackId($fallBackId)
    {
        $this->fallBackId = $fallBackId;
    }

    /**
     */
    public function calcAll()
    {
        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @param bool
     */
    public function setIsNetPrice($isNetPrice)
    {
        $this->isNetPrice = $isNetPrice;
    }

    /**
     * @return bool
     */
    public function getIsNetPrice()
    {
        return $this->isNetPrice;
    }

    /**
     * @param bool
     */
    public function setIsPreset($isPreset)
    {
        $this->isPreset = $isPreset;
    }

    /**
     * @return bool
     */
    public function getIsPreset()
    {
        return $this->isPreset;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getGross()
    {
        $this->calcGross();
        return $this->gross;
    }

    /**
     */
    public function calcGross()
    {
        $gross = 0.0;

        $condition = $this->getConditionFromCart();

        if (isset($condition)) {
            if ($condition === 0.0) {
                $gross = 0.0;
            } else {
                foreach ($this->extras as $extra) {
                    /** @var Extra $extra */
                    if ($extra->leq($condition)) {
                        $gross = $extra->getGross();
                    } else {
                        break;
                    }
                }
            }
        } else {
            $gross = $this->extras[0]->getGross();

            if ($this->cart) {
                $gross = $this->cart->translatePrice($gross);
            }

            if ($this->getExtraType() == 'each') {
                $gross = $this->cart->getCount() * $gross;
            }
        }

        $this->gross = $gross;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function getNet()
    {
        $this->calcNet();
        return $this->net;
    }

    /**
     */
    protected function calcNet()
    {
        $net = 0.0;

        $condition = $this->getConditionFromCart();

        if (isset($condition)) {
            if ($condition === 0.0) {
                $net = 0.0;
            } else {
                foreach ($this->extras as $extra) {
                    /** @var Extra $extra */
                    if ($extra->leq($condition)) {
                        $net = $extra->getNet();
                    } else {
                        break;
                    }
                }
            }
        } else {
            $net = $this->extras[0]->getNet();

            if ($this->cart) {
                $net = $this->cart->translatePrice($net);
            }

            if ($this->getExtraType() == 'each') {
                $net = $this->cart->getCount() * $net;
            }
        }

        $this->net = $net;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return float
     */
    public function getTax()
    {
        $this->calcTax();
        return $this->tax;
    }

    /**
     */
    protected function calcTax()
    {
        $tax = 0.0;

        $condition = $this->getConditionFromCart();

        if (isset($condition)) {
            if ($condition === 0.0) {
                $tax = 0.0;
            } else {
                foreach ($this->extras as $extra) {
                    /** @var Extra $extra */
                    if ($extra->leq($condition)) {
                        $tax = $extra->getTax();
                        $tax = $tax['tax'];
                    } else {
                        break;
                    }
                }
            }
        } else {
            $tax = $this->extras[0]->getTax();

            if ($this->cart) {
                $tax['tax'] = $this->cart->translatePrice($tax['tax']);
            }

            if ($this->getExtraType() == 'each') {
                $tax = $this->cart->getCount() * $tax['tax'];
            } else {
                $tax = $tax['tax'];
            }
        }

        $this->tax = $tax;
    }

    /**
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * @return Extra
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * @param Extra $newExtra
     */
    public function addExtra($newExtra)
    {
        $this->extras[] = $newExtra;
    }

    /**
     * @return string
     */
    public function getExtraType()
    {
        return $this->extratype;
    }

    /**
     * @param string $extratype
     */
    public function setExtraType($extratype)
    {
        $this->extratype = $extratype;
    }

    /**
     * @return float
     */
    public function getFreeFrom()
    {
        return $this->freeFrom;
    }

    /**
     * @param $freeFrom
     */
    public function setFreeFrom($freeFrom)
    {
        $this->freeFrom = $freeFrom;
    }

    /**
     * @return float
     */
    public function getFreeUntil()
    {
        return $this->freeUntil;
    }

    /**
     * @param $freeUntil
     */
    public function setFreeUntil($freeUntil)
    {
        $this->freeUntil = $freeUntil;
    }

    /**
     * @return float
     */
    public function getAvailableFrom()
    {
        return $this->availableFrom;
    }

    /**
     * @param $availableFrom
     */
    public function setAvailableFrom($availableFrom)
    {
        $this->availableFrom = $availableFrom;
    }

    /**
     * @return float
     */
    public function getAvailableUntil()
    {
        return $this->availableUntil;
    }

    /**
     * @param $availableUntil
     */
    public function setAvailableUntil($availableUntil)
    {
        $this->availableUntil = $availableUntil;
    }

    /**
     * @param $price
     * @return bool
     */
    public function isFree($price)
    {
        if (isset($this->freeFrom) || isset($this->freeUntil)) {
            if (isset($this->freeFrom) && $price < $this->freeFrom) {
                return false;
            }
            if (isset($this->freeUntil) && $price > $this->freeUntil) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param $price
     * @return bool
     */
    public function isAvailable($price)
    {
        if (isset($this->availableFrom) && $price < $this->availableFrom) {
            return false;
        }
        if (isset($this->availableUntil) && $price > $this->availableUntil) {
            return false;
        }

        return true;
    }

    /**
     */
    protected function getConditionFromCart()
    {
        $condition = null;

        if ($this->isFree($this->cart->getGross())) {
            return 0.0;
        }

        switch ($this->getExtraType()) {
            case 'by_price':
                $condition = $this->cart->getGross();
                break;
            case 'by_quantity':
            case 'by_number_of_physical_products':
                $condition = $this->cart->getCountPhysicalProducts();
                break;
            case 'by_number_of_virtual_products':
                $condition = $this->cart->getCountVirtualProducts();
                break;
            case 'by_number_of_all_products':
                $condition = $this->cart->getCount();
                break;
            case 'by_service_attribute_1_sum':
                $condition = $this->cart->getSumServiceAttribute1();
                break;
            case 'by_service_attribute_1_max':
                $condition = $this->cart->getMaxServiceAttribute1();
                break;
            case 'by_service_attribute_2_sum':
                $condition = $this->cart->getSumServiceAttribute2();
                break;
            case 'by_service_attribute_2_max':
                $condition = $this->cart->getMaxServiceAttribute2();
                break;
            case 'by_service_attribute_3_sum':
                $condition = $this->cart->getSumServiceAttribute3();
                break;
            case 'by_service_attribute_3_max':
                $condition = $this->cart->getMaxServiceAttribute3();
                break;
            default:
        }

        return $condition;
    }

    /**
     * @return array
     */
    public function getAdditionalArray()
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditionalArray($additional)
    {
        $this->additional = $additional;
    }

    /**
     */
    public function unsetAdditionalArray()
    {
        $this->additional = [];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAdditional($key)
    {
        return $this->additional[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAdditional($key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function unsetAdditional($key)
    {
        if ($this->additional[$key]) {
            unset($this->additional[$key]);
        }
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
