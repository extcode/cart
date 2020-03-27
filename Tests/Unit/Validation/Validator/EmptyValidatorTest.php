<?php

namespace Extcode\Cart\Tests\Unit\Validation\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class EmptyValidatorTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $validatorClassName = \Extcode\Cart\Validation\Validator\EmptyValidator::class;

    /**
     * @var \TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface
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

    public function setup()
    {
        $this->validator = $this->getMockBuilder($this->validatorClassName)
            ->setMethods(['translateErrorMessage'])
            ->getMock();
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsErrorForASimpleString()
    {
        $this->assertTrue($this->validator->validate('a not empty string')->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsNoErrorForAnEmptyString()
    {
        $this->assertFalse($this->validator->validate('')->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsErrorForASimpleNumber()
    {
        $this->assertTrue($this->validator->validate(1)->hasErrors());
        $this->assertTrue($this->validator->validate(1)->hasErrors());
        $this->assertTrue($this->validator->validate(1.5)->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorReturnsNoErrorForANullValue()
    {
        $this->assertFalse($this->validator->validate(null)->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorCreatesTheCorrectErrorForAnEmptySubject()
    {
        $this->assertEquals(1, count($this->validator->validate('a not empty string')->getErrors()));
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForArrays()
    {
        $this->assertFalse($this->validator->validate([])->hasErrors());
        $this->assertTrue($this->validator->validate([1 => 2])->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForCountableObjects()
    {
        $this->assertFalse($this->validator->validate(new \SplObjectStorage())->hasErrors());
    }

    /**
     * @test
     */
    public function emptyValidatorWorksForEmptyCountableObjects()
    {
        $countableObject = new \SplObjectStorage();
        $countableObject->attach(new \stdClass());
        $this->assertTrue($this->validator->validate($countableObject)->hasErrors());
    }
}
