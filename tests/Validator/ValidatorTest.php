<?php

use Fyuze\Validator\Validator;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testValidator()
    {
        $validator = new Validator(
            ['foo' => 'foo'],
            ['foo' => 'required|min:3|max:6']
        );

        $this->assertTrue($validator->passes());
        $this->assertFalse($validator->failed());
    }

    public function testValidatorPopulatesErrors()
    {
        $validator = new Validator(
            ['foo' => 'hi'],
            ['foo' => 'required|min:3|max:6']
        );

        $this->assertTrue($validator->failed());
        $this->assertCount(1, $validator->getErrors());
        $this->assertEquals('Error with rule min on field foo with hi,3', $validator->getErrors()[0]);
    }
}
