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
                'companyId' =>1,
                
            ],
            [
                'name' => 'preset2',
                'companyId' =>1,
                
            ],
            [
                'name' => 'preset3',
                'companyId' =>1,
                
            ]
            
            
        ];

        $this->loadTable($data);
    }
}