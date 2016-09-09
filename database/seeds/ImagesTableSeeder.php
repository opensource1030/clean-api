<?php

/**
 * ImagesTableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class ImagesTableSeeder extends BaseTableSeeder
{
    protected $table = "images";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'originalName'     => "imageName1",
                'filename'      => "imagegoogle",
                'size'      => "imagegoogleapp",
                'pathName'      => "imagegoogleapp"
            ],
            [
                'originalName'     => "imageName2",
                'filename'      => "imagegoogle",
                'size'      => "imagegoogleapp",
                'pathName'      => "imagegoogleapp"
            ],
            [
                'originalName'     => "imageName3",
                'filename'      => "imagegoogle",
                'size'      => "imagegoogleapp",
                'pathName'      => "imagegoogleapp"
            ],
            [
                'originalName'     => "imageName4",
                'filename'      => "imagegoogle",
                'size'      => "imagegoogleapp",
                'pathName'      => "imagegoogleapp"
            ]
        ];

        $this->loadTable($data);
    }
}
$table->string('originalName')->nullable();
                $table->string('filename')->nullable();
                $table->integer('mimeType')->nullable();
                $table->string('size')->nullable();
                $table->string('pathName')->nullable();