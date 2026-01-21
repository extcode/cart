<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AbstractAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AbstractAddress::class)]
class AbstractAddressTest extends UnitTestCase
{
    /**
     * @var MockObject&AbstractAddress
     */
    protected MockObject $address;

    public function setUp(): void
    {
        $this->address = $this->getMockForAbstractClass(AbstractAddress::class);

        parent::setUp();
    }

    #[Test]
    public function toArrayReturnsArray(): void
    {
        $salutation = 'salutation';
        $title = 'title';
        $firstName = 'first name';
        $lastName = 'last name';
        $company = 'company';
        $street = 'street';
        $streetNumber = 'streetNumber';
        $addition = 'addition';
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
        $this->address->setAddition($addition);
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
            'addition' => $addition,
            'zip' => $zip,
            'city' => $city,
            'country' => $country,
            'email' => $email,
            'phone' => $phone,
            'fax' => $fax,
        ];

        self::assertSame(
            $addressArray,
            $this->address->toArray()
        );
    }

    #[Test]
    public function getItemInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->address->getItem()
        );
    }

    #[Test]
    public function setItemSetsItem(): void
    {
        $item = new Item();

        $this->address->setItem($item);

        self::assertSame(
            $item,
            $this->address->getItem()
        );
    }

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getTitle()
        );
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $title = 'title';
        $this->address->setTitle($title);

        self::assertSame(
            $title,
            $this->address->getTitle()
        );
    }

    #[Test]
    public function getSalutationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getSalutation()
        );
    }

    #[Test]
    public function setSalutationSetsSalutation(): void
    {
        $salutation = 'salutation';
        $this->address->setSalutation($salutation);

        self::assertSame(
            $salutation,
            $this->address->getSalutation()
        );
    }

    #[Test]
    public function getFirstNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getFirstName()
        );
    }

    #[Test]
    public function setFirstNameSetsFirstName(): void
    {
        $firstName = 'first name';
        $this->address->setFirstName($firstName);

        self::assertSame(
            $firstName,
            $this->address->getFirstName()
        );
    }

    #[Test]
    public function getLastNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getLastName()
        );
    }

    #[Test]
    public function setLastNameSetsLastName(): void
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertSame(
            $lastName,
            $this->address->getLastName()
        );
    }

    #[Test]
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

    #[Test]
    public function getTitleLastNameWithoutTitleReturnsCorrectConcatenation(): void
    {
        $lastName = 'last name';
        $this->address->setLastName($lastName);

        self::assertEquals(
            $lastName,
            $this->address->getTitleLastName()
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function getCompanyInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCompany()
        );
    }

    #[Test]
    public function setCompanySetsCompany(): void
    {
        $company = 'company';
        $this->address->setCompany($company);

        self::assertSame(
            $company,
            $this->address->getCompany()
        );
    }

    #[Test]
    public function getStreetInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getStreet()
        );
    }

    #[Test]
    public function setStreetSetsStreet(): void
    {
        $street = 'street';
        $this->address->setStreet($street);

        self::assertSame(
            $street,
            $this->address->getStreet()
        );
    }

    #[Test]
    public function getAdditionInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getAddition()
        );
    }

    #[Test]
    public function setAdditionSetsAddition(): void
    {
        $addition = 'addition';
        $this->address->setAddition($addition);

        self::assertSame(
            $addition,
            $this->address->getAddition()
        );
    }

    #[Test]
    public function getZipInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getZip()
        );
    }

    #[Test]
    public function setZipSetsZip(): void
    {
        $zip = 'zip';
        $this->address->setZip($zip);

        self::assertSame(
            $zip,
            $this->address->getZip()
        );
    }

    #[Test]
    public function getCityInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCity()
        );
    }

    #[Test]
    public function setCitySetsCity(): void
    {
        $city = 'city';
        $this->address->setCity($city);

        self::assertSame(
            $city,
            $this->address->getCity()
        );
    }

    #[Test]
    public function getCountryInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getCountry()
        );
    }

    #[Test]
    public function setCountrySetsCountry(): void
    {
        $country = 'country';
        $this->address->setCountry($country);

        self::assertSame(
            $country,
            $this->address->getCountry()
        );
    }

    #[Test]
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getEmail()
        );
    }

    #[Test]
    public function setEmailSetsEmail(): void
    {
        $email = 'email';
        $this->address->setEmail($email);

        self::assertSame(
            $email,
            $this->address->getEmail()
        );
    }

    #[Test]
    public function getPhoneInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getPhone()
        );
    }

    #[Test]
    public function setPhoneSetsPhone(): void
    {
        $phone = 'phone';
        $this->address->setPhone($phone);

        self::assertSame(
            $phone,
            $this->address->getPhone()
        );
    }

    #[Test]
    public function getFaxInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->address->getFax()
        );
    }

    #[Test]
    public function setFaxSetsFax(): void
    {
        $fax = 'fax';
        $this->address->setFax($fax);

        self::assertSame(
            $fax,
            $this->address->getFax()
        );
    }

    #[Test]
    public function getAdditionalInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->address->getAdditional()
        );
    }

    #[Test]
    public function setAdditionalSetsAdditional(): void
    {
        $additional = [
            'additional' => true,
        ];

        $this->address->setAdditional($additional);

        self::assertSame(
            $additional,
            $this->address->getAdditional()
        );
    }

    #[Test]
    public function additionalIsInternallyJsonString(): void
    {
        $additional = [
            'additional' => true,
        ];

        /** @var AccessibleObjectInterface&MockObject&AbstractAddress $address */
        $address = $this->getAccessibleMock(
            AbstractAddress::class,
            [],
            [],
            '',
            false
        );

        self::assertSame(
            '',
            $address->_get('additional')
        );

        $address->setAdditional($additional);

        self::assertSame(
            json_encode($additional),
            $address->_get('additional')
        );
    }

    /**
     * Creates a mock object which allows for calling protected methods and access of protected properties.
     *
     * Note: This method has no native return types on purpose, but only PHPDoc return type annotations.
     * The reason is that the combination of "union types with generics in PHPDoc" and "a subset of those types as
     * native types, but without the generics" tends to confuse PhpStorm's static type analysis (which we want to avoid).
     *
     * @template T of object
     * @param class-string<T> $originalClassName name of class to create the mock object of
     * @param string[]|null $methods name of the methods to mock, null for "mock no methods"
     * @param array $arguments arguments to pass to constructor
     * @param string $mockClassName the class name to use for the mock class
     * @param bool $callOriginalConstructor whether to call the constructor
     * @param bool $callOriginalClone whether to call the __clone method
     * @param bool $callAutoload whether to call any autoload function
     *
     * @return MockObject&AccessibleObjectInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getAccessibleMock(
        string $originalClassName,
        ?array $methods = [],
        array $arguments = [],
        string $mockClassName = '',
        bool $callOriginalConstructor = true,
        bool $callOriginalClone = true,
        bool $callAutoload = true
    ): MockObject {
        $mockBuilder = $this->getMockBuilder($this->buildAccessibleProxy($originalClassName))
            ->addMethods($methods)
            ->setConstructorArgs($arguments)
            ->setMockClassName($mockClassName);

        if (!$callOriginalConstructor) {
            $mockBuilder->disableOriginalConstructor();
        }

        if (!$callOriginalClone) {
            $mockBuilder->disableOriginalClone();
        }

        if (!$callAutoload) {
            $mockBuilder->disableAutoload();
        }

        return $mockBuilder->getMock();
    }
}
