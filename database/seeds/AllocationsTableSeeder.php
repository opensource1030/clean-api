<?php


class AllocationsTableSeeder extends BaseTableSeeder
{
    protected $table = 'allocations';


    public function run()
    {
        $this->deleteTable();

        factory(\WA\DataStore\Allocation\Allocation::class, 100)->create();
    }

}
