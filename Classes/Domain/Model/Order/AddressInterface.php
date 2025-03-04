<?php

namespace Extcode\Cart\Domain\Model\Order;

interface AddressInterface
{
    public function toArray(): array;

    public function setPid(int $pid): void;

    public function getItem(): ?Item;

    public function setItem(Item $item): void;

    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getSalutation(): string;

    public function setSalutation(string $salutation): void;

    public function getFirstName(): string;

    public function setFirstName(string $firstName): void;

    public function getLastName(): string;

    public function setLastName(string $lastName): void;

    public function getSalutationLastName(): string;

    public function getTitleLastName(): string;

    public function getSalutationTitleLastName(): string;

    public function getSalutationOrTitleLastName(): string;

    public function getFullName(): string;

    public function getSalutationFullName(): string;

    public function getTitleFullName(): string;

    public function getSalutationTitleFullName(): string;

    public function getEmail(): string;

    public function setEmail(string $email): void;

    public function getCompany(): string;

    public function setCompany(string $company): void;

    public function getStreet(): string;

    public function setStreet(string $street): void;

    public function getStreetNumber(): string;

    public function setStreetNumber(string $streetNumber): void;

    public function getCity(): string;

    public function setCity(string $city): void;

    public function getAddition(): string;

    public function setAddition(string $addition): void;

    public function getZip(): string;

    public function setZip(string $zip): void;

    public function getCountry(): string;

    public function setCountry(string $country): void;

    public function getPhone(): string;

    public function setPhone(string $phone): void;

    public function getFax(): string;

    public function setFax(string $fax): void;

    public function getAdditional(): array;

    public function setAdditional(array $additional): void;
}
