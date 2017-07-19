<?php
namespace Extcode\Cart\Tests\Unit\Validation\Validator;

/*                                                                        *
 * This script belongs to the Extbase framework.                          *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Empty Validator
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class EmptyValidatorTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
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
