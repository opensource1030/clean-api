<?php

use Illuminate\Database\Seeder;

class AllocationsTableSeeder extends Seeder
{

    public function run()
    {
        factory(\WA\DataStore\Allocation\Allocation::class, 100)->create();
    }

}
