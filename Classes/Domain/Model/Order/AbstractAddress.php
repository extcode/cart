<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

abstract class AbstractAddress extends AbstractEntity
{
    /**
     * @var Item
     */
    protected $item = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $salutation = '';

    /**
     * @var string
     */
    protected $firstName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $company = '';

    /**
     * @var string
     */
    protected $street = '';

    /**
     * @var string
     */
    protected $streetNumber = '';

    /**
     * @var string
     */
    protected $addition = '';

    /**
     * @var string
     */
    protected $zip = '';

    /**
     * @var string
     */
    protected $city = '';

    /**
     * @var string
     */
    protected $country = '';

    /**
     * @var string
     */
    protected $phone = '';

    /**
     * @var string
     */
    protected $fax = '';

    /**
     * @var string
     */
    protected $additional = '';

    /**
     * @return array
     */
    public function toArray(): array
    {
        $address = [
            'salutation' => $this->getSalutation(),
            'title' => $this->getTitle(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'company' => $this->getCompany(),
            'street' => $this->getStreet(),
            'streetNumber' => $this->getStreetNumber(),
            'addition' => $this->getAddition(),
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSalutation(): string
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     */
    public function setSalutation(string $salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getSalutationLastName(): string
    {
        $salutationLastName = [$this->salutation, $this->lastName];
        return implode(' ', array_filter($salutationLastName));
    }

    /**
     * @return string
     */
    public function getTitleLastName(): string
    {
        $titleLastName = [$this->title, $this->lastName];
        return implode(' ', array_filter($titleLastName));
    }

    /**
     * @return string
     */
    public function getSalutationTitleLastName(): string
    {
        $salutationTitleLastName = [$this->salutation, $this->title, $this->lastName];
        return implode(' ', array_filter($salutationTitleLastName));
    }

    /**
     * @return string
     */
    public function getSalutationOrTitleLastName(): string
    {
        if ($this->title) {
            return $this->getTitleLastName();
        }

        return $this->getSalutationLastName();
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        $fullName = [$this->firstName, $this->lastName];
        return implode(' ', array_filter($fullName));
    }

    /**
     * @return string
     */
    public function getSalutationFullName(): string
    {
        $salutationFullName = [$this->salutation, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationFullName));
    }

    /**
     * @return string
     */
    public function getTitleFullName(): string
    {
        $titleFullName = [$this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($titleFullName));
    }

    /**
     * @return string
     */
    public function getSalutationTitleFullName(): string
    {
        $salutationTitleFullName = [$this->salutation, $this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationTitleFullName));
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompany(): string
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany(string $company)
    {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber(string $streetNumber)
    {
        $this->streetNumber = $streetNumber;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddition(): string
    {
        return $this->addition;
    }

    /**
     * @param string $addition
     */
    public function setAddition(string $addition): void
    {
        $this->addition = $addition;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @param string $zip
     */
    public function setZip(string $zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFax(): string
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax(string $fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    /**
     * @param array $additional
     */
    public function setAdditional(array $additional)
    {
        $this->additional = json_encode($additional);
    }
}
