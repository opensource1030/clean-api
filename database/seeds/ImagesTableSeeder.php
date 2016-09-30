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
                'originalName' => 'imageName1',
                'filename' => 'fileImageName1',
                'mimeType' => 'mimeType1',
                'extension' => 'extension1',
                'size' => 40000,
                'url' => 'storage/files/fileImageName1.extension1'
            ],
            [
                'originalName' => 'imageName2',
                'filename' => 'fileImageName2',
                'mimeType' => 'mimeType2',
                'extension' => 'extension2',
                'size' => 50000,
                'url' => 'storage/files/fileImageName2.extension2'
            ],
            [
                'originalName' => 'imageName3',
                'filename' => 'fileImageName3',
                'mimeType' => 'mimeType3',
                'extension' => 'extension3',
                'size' => 60000,
                'url' => 'storage/files/fileImageName3.extension3'
            ],
            [
                'originalName' => 'imageName4',
                'filename' => 'fileImageName4',
                'mimeType' => 'mimeType4',
                'extension' => 'extension4',
                'size' => 70000,
                'url' => 'storage/files/fileImageName4.extension4'
            ]
        ];

        $this->loadTable($data);
    }
}