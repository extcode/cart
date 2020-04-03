<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AddressTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\AbstractAddress
     */
    protected $address;

    public function setUp(): void
    {
        $this->address = $this->getMockForAbstractClass(
            \Extcode\Cart\Domain\Model\Order\AbstractAddress::class
        );
    }

    /**
     * @test
     */
    public function toArrayReturnsArray()
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

        $this->assertSame(
            $addressArray,
            $this->address->toArray()
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $title = 'title';
        $this->address->setTitle($title);

        $this->assertSame(
            $title,
            $this->address->getTitle()
        );
    }

    /**
     * @test
     */
    public function getSalutationInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getSalutation()
        );
    }

    /**
     * @test
     */
    public function setSalutationSetsSalutation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);

        $this->assertSame(
            $salutation,
            $this->address->getSalutation()
        );
    }

    /**
     * @test
     */
    public function getFirstNameInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getFirstName()
        );
    }

    /**
     * @test
     */
    public function setFirstNameSetsFirstName()
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);

        $this->assertSame(
            $firstName,
            $this->address->getFirstName()
        );
    }

    /**
     * @test
     */
    public function getLastNameInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getLastName()
        );
    }

    /**
     * @test
     */
    public function setLastNameSetsLastName()
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertSame(
            $lastName,
            $this->address->getLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationLastNameReturnsConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationLastName()
        );
    }

    /**
     * @test
     */
    public function getTitleLastNameWithoutTitleReturnsCorrectConcatenation()
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $lastName,
            $this->address->getTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getTitleLastNameWithTitleReturnsCorrectConcatenation()
    {
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $title . ' ' . $lastName,
            $this->address->getTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleLastNameWithoutTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleLastNameWithTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $title . ' ' . $lastName,
            $this->address->getSalutationTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationOrTitleLastNameWithoutTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $lastName,
            $this->address->getSalutationOrTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getSalutationOrTitleLastNameWithTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $title . ' ' . $lastName,
            $this->address->getSalutationOrTitleLastName()
        );
    }

    /**
     * @test
     */
    public function getFullNameReturnsCorrectConcatenation()
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $firstName . ' ' . $lastName,
            $this->address->getFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationFullNameReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationFullName()
        );
    }

    /**
     * @test
     */
    public function getTitleFullNameWithoutTitleReturnsCorrectConcatenation()
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $firstName . ' ' . $lastName,
            $this->address->getTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getTitleFullNameWithTitleReturnsCorrectConcatenation()
    {
        $title = 'title';
        $this->address->setTitle($title);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $title . ' ' . $firstName . ' ' . $lastName,
            $this->address->getTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleFullNameWithoutTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getSalutationTitleFullNameWithTitleReturnsCorrectConcatenation()
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);
        $title = 'title';
        $this->address->setTitle($title);
        $firstName = 'first name';
        $this->address->setFirstName($firstName);
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        $this->assertEquals(
            $salutation . ' ' . $title . ' ' . $firstName . ' ' . $lastName,
            $this->address->getSalutationTitleFullName()
        );
    }

    /**
     * @test
     */
    public function getCompanyInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getCompany()
        );
    }

    /**
     * @test
     */
    public function setCompanySetsCompany()
    {
        $company = 'company';
        $this->address->setCompany($company);

        $this->assertSame(
            $company,
            $this->address->getCompany()
        );
    }

    /**
     * @test
     */
    public function getStreetInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getStreet()
        );
    }

    /**
     * @test
     */
    public function setStreetSetsStreet()
    {
        $street = 'street';
        $this->address->setStreet($street);

        $this->assertSame(
            $street,
            $this->address->getStreet()
        );
    }

    /**
     * @test
     */
    public function getZipInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipSetsZip()
    {
        $zip = 'zip';
        $this->address->setZip($zip);

        $this->assertSame(
            $zip,
            $this->address->getZip()
        );
    }

    /**
     * @test
     */
    public function getCityInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getCity()
        );
    }

    /**
     * @test
     */
    public function setCitySetsCity()
    {
        $city = 'city';
        $this->address->setCity($city);

        $this->assertSame(
            $city,
            $this->address->getCity()
        );
    }

    /**
     * @test
     */
    public function getCountryInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getCountry()
        );
    }

    /**
     * @test
     */
    public function setCountrySetsCountry()
    {
        $country = 'country';
        $this->address->setCountry($country);

        $this->assertSame(
            $country,
            $this->address->getCountry()
        );
    }

    /**
     * @test
     */
    public function getEmailInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getEmail()
        );
    }

    /**
     * @test
     */
    public function setEmailSetsEmail()
    {
        $email = 'email';
        $this->address->setEmail($email);

        $this->assertSame(
            $email,
            $this->address->getEmail()
        );
    }

    /**
     * @test
     */
    public function getPhoneInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getPhone()
        );
    }

    /**
     * @test
     */
    public function setPhoneSetsPhone()
    {
        $phone = 'phone';
        $this->address->setPhone($phone);

        $this->assertSame(
            $phone,
            $this->address->getPhone()
        );
    }

    /**
     * @test
     */
    public function getFaxInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->address->getFax()
        );
    }

    /**
     * @test
     */
    public function setFaxSetsFax()
    {
        $fax = 'fax';
        $this->address->setFax($fax);

        $this->assertSame(
            $fax,
            $this->address->getFax()
        );
    }
}
