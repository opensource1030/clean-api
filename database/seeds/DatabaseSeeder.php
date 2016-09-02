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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call(AllocationsTableSeeder::class);
        $this->call(AppsTableSeeder::class);
        $this->call(AssetsTableSeeder::class);
        $this->call(CarriersTableSeeder::class);      
        $this->call(CompanyDomainsTableSeeder::class);
        $this->call(CompanySaml2TableSeeder::class);
        $this->call(DeviceCarriersTableSeeder::class);        
        $this->call(DeviceCompaniesTableSeeder::class);
        $this->call(DeviceModificationsTableSeeder::class);
        $this->call(DevicePricesTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(ModificationsTableSeeder::class);
        $this->call(OAuthTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        //$this->call(PagesTableSeeder::class);
        //$this->call(ProvidersTableSeeder::class);
        $this->call(RequestsTableSeeder::class);        
        $this->call(ServicesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
