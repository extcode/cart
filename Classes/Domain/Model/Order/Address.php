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
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

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
     * @validate NotEmpty
     */
    protected $salutation;

    /**
     * FirstName
     *
     * @var string
     * @validate NotEmpty
     */
    protected $firstName;

    /**
     * LastName
     *
     * @var string
     * @validate NotEmpty
     */
    protected $lastName;

    /**
     * Email
     *
     * @var string
     * @validate NotEmpty
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
     * @validate NotEmpty
     */
    protected $street;

    /**
     * Zip
     *
     * @var string
     * @validate NotEmpty
     */
    protected $zip;

    /**
     * City
     *
     * @var string
     * @validate NotEmpty
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
     *
     * @return void
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
     * @return void
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
     * @return void
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return void
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
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @param string $lastName
     * @return void
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
     * @return void
     */
    public function setStreet($street)
    {
        $this->street = $street;
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }
}
