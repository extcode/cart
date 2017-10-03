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
 * Order Address Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Order Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Salutation
     *
     * @var string
     */
    protected $salutation;

    /**
     * FirstName
     *
     * @var string
     */
    protected $firstName;

    /**
     * LastName
     *
     * @var string
     */
    protected $lastName;

    /**
     * Email
     *
     * @var string
     */
    protected $email;

    /**
     * Company
     *
     * @var string
     */
    protected $company = '';

    /**
     * Street
     *
     * @var string
     */
    protected $street;

    /**
     * Street Number
     *
     * @var string
     */
    protected $streetNumber;

    /**
     * Zip
     *
     * @var string
     */
    protected $zip;

    /**
     * City
     *
     * @var string
     */
    protected $city;

    /**
     * Country
     *
     * @var string
     */
    protected $country;

    /**
     * Phone
     *
     * @var string
     */
    protected $phone = '';

    /**
     * Fax
     *
     * @var string
     */
    protected $fax = '';

    /**
     * Additional
     *
     * @var string
     */
    protected $additional;

    /**
     * Returns AddressArray
     *
     * @return array
     */
    public function toArray()
    {
        $address = [
            'salutation' => $this->getSalutation(),
            'title' => $this->getTitle(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'company' => $this->getCompany(),
            'street' => $this->getStreet(),
            'streetNumber' => $this->getStreetNumber(),
            'zip' => $this->getZip(),
            'city' => $this->getCity(),
            'country' => $this->getCountry(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'fax' => $this->getFax(),
        ];

        return $address;
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
     * Get Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets Title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getSalutationLastName()
    {
        $salutationLastName = [$this->salutation, $this->lastName];
        return implode(' ', array_filter($salutationLastName));
    }

    /**
     * @return string
     */
    public function getTitleLastName()
    {
        $titleLastName = [$this->title, $this->lastName];
        return implode(' ', array_filter($titleLastName));
    }

    /**
     * @return string
     */
    public function getSalutationTitleLastName()
    {
        $salutationTitleLastName = [$this->salutation, $this->title, $this->lastName];
        return implode(' ', array_filter($salutationTitleLastName));
    }

    /**
     * @return string
     */
    public function getSalutationOrTitleLastName()
    {
        if ($this->title) {
            return $this->getTitleLastName();
        } else {
            return $this->getSalutationLastName();
        }
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        $fullName = [$this->firstName, $this->lastName];
        return implode(' ', array_filter($fullName));
    }

    /**
     * @return string
     */
    public function getSalutationFullName()
    {
        $salutationFullName = [$this->salutation, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationFullName));
    }

    /**
     * @return string
     */
    public function getTitleFullName()
    {
        $titleFullName = [$this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($titleFullName));
    }

    /**
     * @return string
     */
    public function getSalutationTitleFullName()
    {
        $salutationTitleFullName = [$this->salutation, $this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationTitleFullName));
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return array
     */
    public function getAdditional()
    {
        return json_decode($this->additional, true);
    }

    /**
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        $this->additional = json_encode($additional);
    }
}
