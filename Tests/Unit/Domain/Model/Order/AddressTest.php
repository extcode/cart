<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lorenz <ext.cart@extco.de>, extco.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Address Test
 *
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class AddressTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Address
     */
    protected $address;

    /**
     *
     */
    public function setUp()
    {
        $this->address = new \Extcode\Cart\Domain\Model\Order\Address();
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

        $address = new \Extcode\Cart\Domain\Model\Order\Address();
        $address->setSalutation($salutation);
        $address->setTitle($title);
        $address->setFirstName($firstName);
        $address->setLastName($lastName);
        $address->setCompany($company);
        $address->setStreet($street);
        $address->setStreetNumber($streetNumber);
        $address->setZip($zip);
        $address->setCity($city);
        $address->setCountry($country);
        $address->setEmail($email);
        $address->setPhone($phone);
        $address->setFax($fax);

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
            $address->toArray()
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
        $this->assertNull(
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
