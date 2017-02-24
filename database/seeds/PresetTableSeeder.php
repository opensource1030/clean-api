<?php

class PresetTableSeeder extends BaseTableSeeder
{
    protected $table = 'presets';

    public function run()
    {
        $this->deleteTable();
        factory(\WA\DataStore\Preset\Preset::class, 60)->create();
    }
}