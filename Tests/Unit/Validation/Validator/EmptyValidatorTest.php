<?php

namespace Extcode\Cart\Tests\Unit\Validation\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Validation\Validator\EmptyValidator;
use TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class EmptyValidatorTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $validatorClassName = EmptyValidator::class;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @param array $options
     * @return mixed
     */
    protected function getValidator($options = [])
    {
        $validator = new $this->validatorClassName($options);
        return $validator;
    }

    /**
     * @param array $options
     */
    protected function validatorOptions($options)
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

    /**
     * @test
     */
    public function emptyValidatorReturnsErrorForASimpleString()
    {
        self::assertTrue($this->validator->validate('a not empty string')->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsNoErrorForAnEmptyString()
    {
        self::assertFalse($this->validator->validate('')->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsErrorForASimpleNumber()
    {
        self::assertTrue($this->validator->validate(1)->hasErrors());
        self::assertTrue($this->validator->validate(1)->hasErrors());
        self::assertTrue($this->validator->validate(1.5)->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsNoErrorForANullValue()
    {
        self::assertFalse($this->validator->validate(null)->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorCreatesTheCorrectErrorForAnEmptySubject()
    {
        self::assertEquals(1, count($this->validator->validate('a not empty string')->getErrors()));
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForArrays()
    {
        self::assertFalse($this->validator->validate([])->hasErrors());
        self::assertTrue($this->validator->validate([1 => 2])->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForCountableObjects()
    {
        self::assertFalse($this->validator->validate(new \SplObjectStorage())->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForEmptyCountableObjects()
    {
        $countableObject = new \SplObjectStorage();
        $countableObject->attach(new \stdClass());
        self::assertTrue($this->validator->validate($countableObject)->hasErrors());
    }
}
