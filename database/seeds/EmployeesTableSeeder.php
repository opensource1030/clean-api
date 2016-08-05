<?php

use Illuminate\Database\Seeder;

class EmployeesTableSeeder extends Seeder
{

    public function run()
    {
        factory(\WA\DataStore\Employee\Employee::class, 40)->create();
    }

}
