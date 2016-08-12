<?php


class AssetsTableSeeder extends BaseTableSeeder
{

    protected $table = 'assets';

    public function run()
    {
        $this->deleteTable();
        factory(\WA\DataStore\Asset\Asset::class, 50)->create();
    }

}
