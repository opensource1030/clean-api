<?php


namespace WA\Testing\Services;

use WA\Services\Validation\Laravel\DummyValidator;
use WA\Testing\TestCase;

class DummyValidatorTest extends TestCase
{

    /**
     * @var \WA\Services\Validation\Laravel\DummyValidator
     */
    private $validator;
    /**
     * @var \WA\Services\Validation\Laravel\DummyValidator
     */
    private $validatable;


    public function setUp()
    {
        parent::setUp();

        $this->validator = new DummyValidator(\Mockery::mock('Illuminate\Validation\Factory'));
        $this->validatable = new DummyValidator($this->app->make('validator'));
    }

    /**
     * @expectedException \TypeError
     */
    public function testValidationThrowsExceptionOnWrongDependency()
    {
        new DummyValidator(new \StdClass());

    }

    /**
     * @expectedException \TypeError
     */
    public function testWithMethodThrowsExcpetionIfNotArray()
    {
        $this->validator->with('some random string');
    }

    public function testCanPassesValidation()
    {

        $this->assertTrue($this->validatable->with($this->getValidSavableData())->passes());
    }

    private function getValidSavableData()
    {
        return
            [
                'email' => 'dev@testing.com',
                'firstName' => 'John-Appleseed',
                'username' => 'john.appleseed'
            ];
    }

    public function testFailValidation()
    {
        $this->assertFalse($this->validatable->with($this->getInValidSavableData())->passes());

        $this->assertEquals(1, count($this->validatable->errors()));
        $this->assertInstanceOf('Illuminate\Support\MessageBag', $this->validatable->errors());

    }

    private function getInValidSavableData()
    {
        return
            [
                'email' => '',
                'firstName' => '123345',
                'username' => 'john.appleseed'
            ];
    }


}
