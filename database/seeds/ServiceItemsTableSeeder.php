<?php

/**
 * ServiceItemsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class ServiceItemsTableSeeder extends BaseTableSeeder
{
    protected $table = 'service_items';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [
            [   
                'serviceId'     => 1,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 500,
                'unit'          => 'minutes',
                'cost'          => 10,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'data',
                'description'   => '',
                'value'         => 2,
                'unit'          => 'Gb',
                'cost'          => 10,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 100,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [   
                'serviceId'     => 1,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 50,
                'unit'          => 'minutes',
                'cost'          => 5,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'data',
                'description'   => '',
                'value'         => 0.5,
                'unit'          => 'Gb',
                'cost'          => 10,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 50,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'addon',
                'description'   => 'Ar sa Kiyo Ariquitaum',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 10,
                'domain'        => '',
            ],
            [
                'serviceId'     => 1,
                'category'          => 'addon',
                'description'   => 'Taum, Taum',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 15,
                'domain'        => '',
            ],
            [   
                'serviceId'     => 2,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 300,
                'unit'          => 'minutes',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'data',
                'description'   => '',
                'value'         => 1,
                'unit'          => 'Gb',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 100,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [   
                'serviceId'     => 2,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 300,
                'unit'          => 'minutes',
                'cost'          => 25,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'data',
                'description'   => '',
                'value'         => 1,
                'unit'          => 'Gb',
                'cost'          => 15,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 50,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'addon',
                'description'   => 'Sa Matao Pako!!!',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 15,
                'domain'        => '',
            ],
            [
                'serviceId'     => 2,
                'category'          => 'addon',
                'description'   => 'ozu k kalooo',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 10,
                'domain'        => '',
            ],
            [   
                'serviceId'     => 3,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 300,
                'unit'          => 'minutes',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'data',
                'description'   => '',
                'value'         => 6,
                'unit'          => 'Gb',
                'cost'          => 15,
                'domain'        => 'domestic',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 100,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'domestic',
            ],
            [   
                'serviceId'     => 3,
                'category'          => 'voice',
                'description'   => '',
                'value'         => 100,
                'unit'          => 'minutes',
                'cost'          => 10,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'data',
                'description'   => '',
                'value'         => 0.5,
                'unit'          => 'Gb',
                'cost'          => 5,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'messaging',
                'description'   => '',
                'value'         => 50,
                'unit'          => 'messages',
                'cost'          => 5,
                'domain'        => 'international',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'addon',
                'description'   => 'Buahh loko que palo...',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 15,
                'domain'        => '',
            ],
            [
                'serviceId'     => 3,
                'category'          => 'addon',
                'description'   => 'ultimo addon jejeje',
                'value'         => 0,
                'unit'          => '',
                'cost'          => 25,
                'domain'        => '',
            ]
        ];

        $this->loadTable($data);
    }
}
