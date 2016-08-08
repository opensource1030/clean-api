<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

class DumpExceptionsTest extends TestCase
{

    use ModelHelpers;

    private $className = 'WA\DataStore\DumpExceptions';

    /**
     * @var Wa\DataStore\DumpExceptions
     */
    protected $dumpExceptions;

    public function setUp()
    {
        parent::setUp();

        $this->dumpExceptions = $this->app->make($this->className);
    }

    public function testHasTimestampAttribute()
    {
        $ref = new \ReflectionClass($this->className);

        $this->assertTrue($ref->getProperty('timestamps')->getValue(new $this->className));
    }


    public function testBelongsToRelationships()
    {
        $this->assertBelongsTo('carrierDump', $this->className);
    }

    public function testGetsActionLinks()
    {
        $this->assertTrue((bool)strstr($this->dumpExceptions->getActionLinks(), 'button-group'));
    }

}
