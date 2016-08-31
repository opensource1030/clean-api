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

        $this->call(UsersTableSeeder::class);
        $this->call(AllocationsTableSeeder::class);
        $this->call(OAuthTableSeeder::class);
        $this->call(AssetsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(PagesTableSeeder::class);
        $this->call(CompanySaml2TableSeeder::class);
        $this->call(CarriersTableSeeder::class);        
        $this->call(ModificationsTableSeeder::class);
        $this->call(ProvidersTableSeeder::class);
        $this->call(PricesTableSeeder::class);
        $this->call(DeviceCarriersTableSeeder::class);          
        $this->call(DeviceModificationsTableSeeder::class);
        $this->call(DeviceProvidersTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(CompanyDomainsTableSeeder::class);
        $this->call(AppsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        $this->call(RequestsTableSeeder::class);        

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
