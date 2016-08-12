<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AllocationsTableSeeder::class);
        $this->call(OAuthTableSeeder::class);
        $this->call(AssetsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(PagesTableSeeder::class);
    }

}
