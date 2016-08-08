<?php
namespace WA\DataStore;

use WA\Testing\TestCase;
use Way\Tests\ModelHelpers;

use \File;
use \org\bovigo\vfs\vfsStream;

class CompanyTest extends TestCase
{
    use ModelHelpers;

    private $className = 'WA\DataStore\Company\Company';

    /**
     * @var \WA\DataStore\Company\Company
     */
    protected $company;

    public function setUp()
    {
        $this->markTestSkipped('Not working');
        parent::setUp();

        $this->company = $this->app->make($this->className);
    }

    public function testHasManyRelationships()
    {
        $this->assertHasMany('accountSummaries', $this->className);

        $this->assertHasMany('lineSummaries', $this->className);

        $this->assertHasMany('wirelessLineDetails', $this->className);

        $this->assertHasMany('users', $this->className);

//        $this->assertHasMany('poolBases', $this->className);
    }


    public function testBelongsToManyRelationships()
    {
        $this->assertBelongsToMany('carriers', $this->className);

        $this->assertBelongsToMany('dumps', $this->className);

        $this->assertBelongsToMany('poolGroups', $this->className);
    }


    public function testMakesDirectoryFromDefaultSuppliedArrayOfOptions()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
            $this->markTestSkipped(
                "This test doesn't work properly on Windows."
            );
        }

        $this->company->name = "CarrierName";
        $determinant = ['Random', 'Stuff', 'To', 'Make'];

        $filePath = vfsStream::setup(implode(DIRECTORY_SEPARATOR, $determinant));


        $file = File::shouldReceive(
            [
                'isDirectory'   => true,
                'makeDirectory' => $filePath
            ]
        );

        $this->assertEquals('Random', $filePath->getName());

        $result = $this->company->makeDirectory($determinant, $file->getMock());

        $this->assertEquals('/CarrierName/Data/Random/Stuff/To/Make/', $result);


    }

    public function testReturnsDirectoryPathIfAlreadyExisting()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
            $this->markTestSkipped(
                "This test doesn't work properly on Windows."
            );
        }

        $determinant = ['Random', 'Stuff', 'To', 'Make'];


        $file = File::shouldReceive(
            [
                'isDirectory' => true
            ]
        );


        $expect = DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'Data' . DIRECTORY_SEPARATOR . 'Random' . DIRECTORY_SEPARATOR . 'Stuff' . DIRECTORY_SEPARATOR . 'To' . DIRECTORY_SEPARATOR . 'Make' . DIRECTORY_SEPARATOR;

        $this->assertEquals($expect, $this->company->makeDirectory($determinant, $file->getMock()));
    }


    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Cannot create the directory //Random/Stuff/
     */
    public function testThrowsExceptionIfCannotMakeDirectory()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
            $this->markTestSkipped(
                "This test doesn't work properly on Windows."
            );
        }

        $this->markTestIncomplete('New dir schema');

        $determinant = ['Random', 'Stuff'];

        $file = File::shouldReceive(
            [

                'isDirectory' => false
            ]
        );

        $this->company->makeDirectory($determinant);
    }


    public function testListsAllTheFileInTheDirectoryWithRightExtensions()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
            $this->markTestSkipped(
                "This test doesn't work properly on Windows."
            );
        }

        $filePath = "Some/Path";

        vfsStream::setup($filePath);

        $txt = File::shouldReceive('getExtension')->andReturn('txt');
        $csv = File::shouldReceive('getExtension')->andReturn('csv');
        $xml = File::shouldReceive('getExtension')->andReturn('xml');
        $tab = File::shouldReceive('getExtension')->andReturn('tab');

        $structure = [

            [
                "Some" => [
                    'Path' => [
                        $txt->getMock(),
                        $csv->getMock(),
                        $xml->getMock(),
                        $tab->getMock()

                    ]
                ]
            ]
        ];

        $files = vfsStream::create($structure);

        $this->assertCount(2, $files->getChildren());

//
        $file = File::shouldReceive(
            [
                'isDirectory' => true,
                'allFiles'    => $structure[ 0 ][ 'Some' ][ 'Path' ]
            ]
        )->with($filePath);


        $listedFiles = $this->company->listDirectoryFiles($filePath, $file->getMock());
//
//
        $this->assertNotEmpty($listedFiles);

    }


    public function testSkipsDirectoryWithoutTheExpectedExtension()
    {
        if ( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
            $this->markTestSkipped(
                "This test doesn't work properly on Windows."
            );
        }

        $filePath = "Some/Path";

        $doc = File::shouldReceive('getExtension')->andReturn('doc');
        $jpg = File::shouldReceive('getExtension')->andReturn('jpg');

        $structure = [

            [
                "Some" => [
                    'Path' => [
                        $doc->getMock(),
                        $jpg->getMock()

                    ]
                ]
            ]
        ];

        $file = File::shouldReceive(
            [
                'isDirectory' => true,
                'allFiles'    => $structure[ 0 ][ 'Some' ][ 'Path' ]
            ]
        )->with($filePath);

        $files = $this->company->listDirectoryFiles($filePath, $file->getMock());

        $this->assertEmpty($files);
    }
}
