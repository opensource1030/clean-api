<?php

/**
 * AppsTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class AddonsTableSeeder extends BaseTableSeeder
{
    protected $table = 'addons';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [

            [
                'name' => 'Addon 1 30GB',
                'cost' => 30,
                'serviceId' => 1,
            ],
            [
                'name' => 'Addon 2 20GB',
                'cost' => 20,
                'serviceId' => 1,
            ],
            [
                'name' => 'Addon 3 40GB',
                'cost' => 40,
                'serviceId' => 1,
            ],
            [
                'name' => 'Addon 4 50GB',
                'cost' => 50,
                'serviceId' => 1,
            ],
            [
                'name' => 'Addon 1 40GB',
                'cost' => 40,
                'serviceId' => 2,
            ],
            [
                'name' => 'Addon 3 20GB',
                'cost' => 20,
                'serviceId' => 2,
            ],
            [
                'name' => 'Addon 4 40GB',
                'cost' => 40,
                'serviceId' => 2,
            ],
            [
                'name' => 'Addon 2 20GB',
                'cost' => 20,
                'serviceId' => 3,
            ],
        ];

        $this->loadTable($data);
    }
}
