<?php


class AllocationsTableSeeder extends BaseTableSeeder
{
    protected $table = 'allocations';

    public function run()
    {
        $this->deleteTable();

        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 1]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 2]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 3]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 4]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 5]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 6]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 7]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 8]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 9]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 10]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 11]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 12]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 13]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 14]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 15]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 16]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 17]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 18]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 19]);
        factory(\WA\DataStore\Allocation\Allocation::class, 1)->create(['userId' => 20]);
    }
}
