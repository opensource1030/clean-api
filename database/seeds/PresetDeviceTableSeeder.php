<?php

class PresetDeviceTableSeeder extends BaseTableSeeder
{
    protected $table = 'preset_devices';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'presetId' => 1,
                'deviceId' => 5
            ],
            [
                'presetId' => 1,
                'deviceId' => 12
            ],
            [
                'presetId' => 1,
                'deviceId' => 27
            ],
            [
                'presetId' => 1,
                'deviceId' => 39
            ],
            [
                'presetId' => 2,
                'deviceId' => 5
            ],
            [
                'presetId' => 2,
                'deviceId' => 12
            ],
            [
                'presetId' => 2,
                'deviceId' => 27
            ],
            [
                'presetId' => 2,
                'deviceId' => 39
            ],
            [
                'presetId' => 3,
                'deviceId' => 5
            ],
            [
                'presetId' => 3,
                'deviceId' => 12
            ],
            [
                'presetId' => 3,
                'deviceId' => 27
            ],
            [
                'presetId' => 3,
                'deviceId' => 39
            ],
        ];

        $this->loadTable($data);
    }
}