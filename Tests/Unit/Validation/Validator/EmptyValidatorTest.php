<?php

namespace Extcode\Cart\Tests\Unit\Validation\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Validation\Validator\EmptyValidator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(EmptyValidator::class)]
class EmptyValidatorTest extends UnitTestCase
{
    protected string $validatorClassName = EmptyValidator::class;

    protected ValidatorInterface $validator;

    /**
     * @param array $options
     */
    protected function getValidator(array $options = []): ValidatorInterface
    {
        return new $this->validatorClassName($options);
    }

    /**
     * @param array $options
     */
    protected function validatorOptions(array $options): void
    {
        $this->validator = $this->getValidator($options);
    }

    public function setUp(): void
    {
        $this->validator = $this->getMockBuilder($this->validatorClassName)
            ->onlyMethods(['translateErrorMessage'])
            ->getMock();

        parent::setUp();
    }

    #[Test]
    public function emptyValidatorReturnsErrorForASimpleString(): void
    {
        self::assertTrue($this->validator->validate('a not empty string')->hasErrors());
    }

    #[Test]
    public function emptyValidatorReturnsNoErrorForAnEmptyString(): void
    {
        self::assertFalse($this->validator->validate('')->hasErrors());
    }

    #[Test]
    public function emptyValidatorReturnsErrorForASimpleNumber(): void
    {
        self::assertTrue($this->validator->validate(1)->hasErrors());
        self::assertTrue($this->validator->validate(1)->hasErrors());
        self::assertTrue($this->validator->validate(1.5)->hasErrors());
    }

    #[Test]
    public function emptyValidatorReturnsNoErrorForANullValue(): void
    {
        self::assertFalse($this->validator->validate(null)->hasErrors());
    }

    #[Test]
    public function emptyValidatorCreatesTheCorrectErrorForAnEmptySubject(): void
    {
        self::assertEquals(1, count($this->validator->validate('a not empty string')->getErrors()));
    }

    #[Test]
    public function emptyValidatorWorksForArrays(): void
    {
        self::assertFalse($this->validator->validate([])->hasErrors());
        self::assertTrue($this->validator->validate([1 => 2])->hasErrors());
    }

    #[Test]
    public function emptyValidatorWorksForCountableObjects(): void
    {
        self::assertFalse($this->validator->validate(new \SplObjectStorage())->hasErrors());
    }

    #[Test]
    public function emptyValidatorWorksForEmptyCountableObjects(): void
    {
        $countableObject = new \SplObjectStorage();
        $countableObject->attach(new \stdClass());
        self::assertTrue($this->validator->validate($countableObject)->hasErrors());
    }
}
