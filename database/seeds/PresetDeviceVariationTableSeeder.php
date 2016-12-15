<?php

class PresetDeviceVariationTableSeeder extends BaseTableSeeder
{
    protected $table = 'preset_deviceVariations';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'presetId' => 1,
                'deviceVariationId' => 1
            ],
            [
                'presetId' => 1,
                'deviceVariationId' => 2
            ],
            [
                'presetId' => 2,
                'deviceVariationId' => 3
            ],
            [
                'presetId' => 3,
                'deviceVariationId' => 1
            ],
            [
                'presetId' => 3,
                'deviceVariationId' => 2
            ],
            [
                'presetId' => 3,
                'deviceVariationId' => 3
            ],
        ];

        $this->loadTable($data);
    }
}