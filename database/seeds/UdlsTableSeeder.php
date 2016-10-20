<?php

class UdlsTableSeeder extends BaseTableSeeder
{
    protected $table = 'udls';

    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'companyId' => 1,
                'name' => 'position',
                'label' => 'Position',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 1,
                'name' => 'level',
                'label' => 'Level',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 1,
                'name' => 'division',
                'label' => 'Division',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 1,
                'name' => 'costcenter',
                'label' => 'Cost Center',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 2,
                'name' => 'sector',
                'label' => 'Sector',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 2,
                'name' => 'vehicle',
                'label' => 'Vehicle',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 2,
                'name' => 'radio',
                'label' => 'Radio',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 2,
                'name' => 'division',
                'label' => 'Division',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 3,
                'name' => 'position',
                'label' => 'Position',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 3,
                'name' => 'sector',
                'label' => 'Sector',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 3,
                'name' => 'vehicle',
                'label' => 'Vehicle',
                'legacyUdlField' => null
            ],
            [
                'companyId' => 3,
                'name' => 'costcenter',
                'label' => 'Cost Center',
                'legacyUdlField' => null
            ]
	    ];

	    $this->loadTable($data);
    }
}