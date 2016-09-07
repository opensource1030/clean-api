<?php


class DevicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'devices';


    public function run()
    {
        $this->deleteTable();

        factory(\WA\DataStore\Device\Device::class, 40)->create();
    }

}
