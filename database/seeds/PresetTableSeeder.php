<?php

class PresetTableSeeder extends BaseTableSeeder
{
    protected $table = 'presets';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'name' => 'preset1',
                
            ],
            [
                'name' => 'preset2',
                
            ],
            [
                'name' => 'preset3',
                
            ]
            
            
        ];

        $this->loadTable($data);
    }
}