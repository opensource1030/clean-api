<?php

class PresetImageTableSeeder extends BaseTableSeeder
{
    protected $table = 'preset_images';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'presetId' => 1,
                'imageId' => 1
            ],
            [
                'presetId' => 1,
                'imageId' => 2
            ],
            [
                'presetId' => 1,
                'imageId' => 3
            ],
            [
                'presetId' => 1,
                'imageId' => 4
            ],
             [
                'presetId' => 2,
                'imageId' => 1
            ],
            [
                'presetId' => 2,
                'imageId' => 2
            ],
            [
                'presetId' => 2,
                'imageId' => 3
            ],
            [
                'presetId' => 2,
                'imageId' => 4
            ],
            [
                'presetId' => 3,
                'imageId' => 1
            ],
            [
                'presetId' => 3,
                'imageId' => 2
            ],
            [
                'presetId' => 3,
                'imageId' => 3
            ],
            [
                'presetId' => 3,
                'imageId' => 4
            ],
        ];

        $this->loadTable($data);
    }
}