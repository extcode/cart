<?php

declare(strict_types=1);

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
    protected ?Item $item = null;

    protected string $title = '';

    protected string $salutation = '';

    protected string $firstName = '';

    protected string $lastName = '';

    protected string $email = '';

    protected string $company = '';

    protected string $street = '';

    protected string $streetNumber = '';

    protected string $addition = '';

    protected string $zip = '';

    protected string $city = '';

    protected string $country = '';

    protected string $phone = '';

    protected string $fax = '';

    protected string $additional = '';

    public function toArray(): array
    {
        return [
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
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(Item $item): void
    {
        $this->item = $item;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSalutation(): string
    {
        return $this->salutation;
    }

    public function setSalutation(string $salutation): void
    {
        $this->salutation = $salutation;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getSalutationLastName(): string
    {
        $salutationLastName = [$this->salutation, $this->lastName];
        return implode(' ', array_filter($salutationLastName));
    }

    public function getTitleLastName(): string
    {
        $titleLastName = [$this->title, $this->lastName];
        return implode(' ', array_filter($titleLastName));
    }

    public function getSalutationTitleLastName(): string
    {
        $salutationTitleLastName = [$this->salutation, $this->title, $this->lastName];
        return implode(' ', array_filter($salutationTitleLastName));
    }

    public function getSalutationOrTitleLastName(): string
    {
        if ($this->title) {
            return $this->getTitleLastName();
        }

        return $this->getSalutationLastName();
    }

    public function getFullName(): string
    {
        $fullName = [$this->firstName, $this->lastName];
        return implode(' ', array_filter($fullName));
    }

    public function getSalutationFullName(): string
    {
        $salutationFullName = [$this->salutation, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationFullName));
    }

    public function getTitleFullName(): string
    {
        $titleFullName = [$this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($titleFullName));
    }

    public function getSalutationTitleFullName(): string
    {
        $salutationTitleFullName = [$this->salutation, $this->title, $this->firstName, $this->lastName];
        return implode(' ', array_filter($salutationTitleFullName));
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): void
    {
        $this->company = $company;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    public function getStreetNumber(): string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): void
    {
        $this->streetNumber = $streetNumber;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getAddition(): string
    {
        return $this->addition;
    }

    public function setAddition(string $addition): void
    {
        $this->addition = $addition;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getFax(): string
    {
        return $this->fax;
    }

    public function setFax(string $fax): void
    {
        $this->fax = $fax;
    }

    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    public function setAdditional(array $additional): void
    {
        $this->additional = json_encode($additional);
    }
}
