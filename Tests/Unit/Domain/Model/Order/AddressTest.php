<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AbstractAddress;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AddressTest extends UnitTestCase
{
    /**
     * @var AbstractAddress
     */
    protected $address;

    public function setUp(): void
    {
        $this->address = $this->getMockForAbstractClass(
            AbstractAddress::class
        );
    }

    /**
     * @test
     */
    public function toArrayReturnsArray(): void
    {
        $salutation = 'salutation';
        $title = 'title';
        $firstName = 'first name';
        $lastName = 'last name';
        $company = 'company';
        $street = 'street';
        $streetNumber = 'streetNumber';
        $zip = 'zip';
        $city = 'city';
        $country = 'country';
        $email = 'email';
        $phone = 'phone';
        $fax = 'fax';

        $this->address->setSalutation($salutation);
        $this->address->setTitle($title);
        $this->address->setFirstName($firstName);
        $this->address->setLastName($lastName);
        $this->address->setCompany($company);
        $this->address->setStreet($street);
        $this->address->setStreetNumber($streetNumber);
        $this->address->setZip($zip);
        $this->address->setCity($city);
        $this->address->setCountry($country);
        $this->address->setEmail($email);
        $this->address->setPhone($phone);
        $this->address->setFax($fax);

        $addressArray = [
            'salutation' => $salutation,
            'title' => $title,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'company' => $company,
            'street' => $street,
            'streetNumber' => $streetNumber,
            'zip' => $zip,
            'city' => $city,
            'country' => $country,
            'email' => $email,
            'phone' => $phone,
            'fax' => $fax
        ];

        self::assertSame(
            $addressArray,
            $this->address->toArray()
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle(): void
    {
        $title = 'title';
        $this->address->setTitle($title);

        self::assertSame(
            $title,
            $this->address->getTitle()
        );
    }

    /**
     * @test
     */
    public function getSalutationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getSalutation()
        );
    }

    /**
     * @test
     */
    public function setSalutationSetsSalutation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);

        self::assertSame(
            $salutation,
            $this->address->getSalutation()
        );
    }

    /**
     * @test
     */
    public function getFirstNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getFirstName()
        );
    }

    /**
     * @test
     */
    public function setFirstNameSetsFirstName(): void
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);

        self::assertSame(
            $firstName,
            $this->address->getFirstName()
        );
    }

    /**
     * @test
     */
    public function getLastNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getLastName()
        );
    }

    /**
     * @test
     */
    public function setLastNameSetsLastName(): void
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertSame(
            $lastName,
            $this->address->getLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationLastNameReturnsConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationLastName()
        );
    }

    /**
     * @test
     */
    public function getTitleLastNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $lastName,
            $this->address->getTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getTitleLastNameWithTitleReturnsCorrectConcatenation(): void
    {
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $title . ' ' . $lastName,
            $this->address->getTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleLastNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleLastNameWithTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $title . ' ' . $lastName,
            $this->address->getSalutationTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationOrTitleLastNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationOrTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationOrTitleLastNameWithTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $title . ' ' . $lastName,
            $this->address->getSalutationOrTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getFullNameReturnsCorrectConcatenation(): void
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $firstName . ' ' . $lastName,
            $this->address->getFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationFullNameReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationFullName()
        );
    }

    /**
     * @test
     */
    public function getTitleFullNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $firstName . ' ' . $lastName,
            $this->address->getTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getTitleFullNameWithTitleReturnsCorrectConcatenation(): void
    {
        $title = 'title';
        $this->address->setTitle($title);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $title . ' ' . $firstName . ' ' . $lastName,
            $this->address->getTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleFullNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleFullNameWithTitleReturnsCorrectConcatenation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $salutation . ' ' . $title . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getCompanyInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCompany()
        );
    }

    /**
     * @test
     */
    public function setCompanySetsCompany(): void
    {
        $company = 'company';
        $this->address->setCompany($company);

        self::assertSame(
            $company,
            $this->address->getCompany()
        );
    }

    /**
     * @test
     */
    public function getStreetInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getStreet()
        );
    }

    /**
     * @test
     */
    public function setStreetSetsStreet(): void
    {
        $street = 'street';
        $this->address->setStreet($street);

        self::assertSame(
            $street,
            $this->address->getStreet()
        );
    }

    /**
     * @test
     */
    public function getZipInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipSetsZip(): void
    {
        $zip = 'zip';
        $this->address->setZip($zip);

        self::assertSame(
            $zip,
            $this->address->getZip()
        );
    }

    /**
     * @test
     */
    public function getCityInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCity()
        );
    }

    /**
     * @test
     */
    public function setCitySetsCity(): void
    {
        $city = 'city';
        $this->address->setCity($city);

        self::assertSame(
            $city,
            $this->address->getCity()
        );
    }

    /**
     * @test
     */
    public function getCountryInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCountry()
        );
    }

    /**
     * @test
     */
    public function setCountrySetsCountry(): void
    {
        $country = 'country';
        $this->address->setCountry($country);

        self::assertSame(
            $country,
            $this->address->getCountry()
        );
    }

    /**
     * @test
     */
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailSetsEmail(): void
    {
        $email = 'email';
        $this->address->setEmail($email);

        self::assertSame(
            $email,
            $this->address->getEmail()
        );
    }

    /**
     * @test
     */
    public function getPhoneInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getPhone()
        );
    }

    /**
     * @test
     */
    public function setPhoneSetsPhone(): void
    {
        $phone = 'phone';
        $this->address->setPhone($phone);

        self::assertSame(
            $phone,
            $this->address->getPhone()
        );
    }

    /**
     * @test
     */
    public function getFaxInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getFax()
        );
    }

    /**
     * @test
     */
    public function setFaxSetsFax(): void
    {
        $fax = 'fax';
        $this->address->setFax($fax);

        self::assertSame(
            $fax,
            $this->address->getFax()
        );
    }
}
