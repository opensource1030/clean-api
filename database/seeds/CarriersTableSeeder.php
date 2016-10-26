<?php

class CarriersTableSeeder extends BaseTableSeeder
{
    protected $table = 'carriers';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'name' => 'verizon',
                'presentation' => 'Verizon',
                'active' => 1,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],

            [
                'name' => 'att',
                'presentation' => 'ATT',
                'active' => 1,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 'bt_mobile',
                'presentation' => 'BT Mobile',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 'ipass',
                'presentation' => 'iPass',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],

            [
                'name' => 'rogers',
                'presentation' => 'Rogers',
                'active' => 1,
                'shortName' => 'shortname',
                'locationId' => 40,
            ],
            [
                'name' => 'sprint',
                'presentation' => 'Sprint',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 't_mobile',
                'presentation' => 'T-Mobile',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 't_mobile_de',
                'presentation' => 'T-Mobile DE',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 83,
            ],
            [
                'name' => 'us_cellular',
                'presentation' => 'US Cellular',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 'system',
                'presentation' => 'System',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 236,
            ],
            [
                'name' => 'bell_canada',
                'presentataion' => 'BellCanada',
                'active' => 1,
                'shortName' => 'shortname',
                'locationId' => 40,
            ],
            [
                'name' => 'vodafone_uk',
                'presentation' => 'VodafoneUK',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 235,
            ],
            [
                'name' => 'vodafone_de',
                'presentation' => 'VodafoneDE',
                'active' => 0,
                'shortName' => 'shortname',
                'locationId' => 83,
            ],
        ];

        $this->loadTable($data);
    }
}
