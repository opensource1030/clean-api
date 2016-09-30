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
        switch(DB::getDriverName()) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = OFF');
                break;
        }

        $this->call(AddressTableSeeder::class);
        $this->call(AllocationsTableSeeder::class);
        $this->call(AppsTableSeeder::class);
        $this->call(AssetsTableSeeder::class);
        $this->call(CarriersTableSeeder::class);      
        $this->call(CompanyDomainsTableSeeder::class);
        $this->call(CompanySaml2TableSeeder::class);
        $this->call(ConditionsTableSeeder::class);        
        $this->call(DeviceCarriersTableSeeder::class);        
        $this->call(DeviceCompaniesTableSeeder::class);
        $this->call(DeviceModificationsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(ImagesTableSeeder::class);
        $this->call(ModificationsTableSeeder::class);
        $this->call(OAuthTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        $this->call(PricesTableSeeder::class);
        //$this->call(PagesTableSeeder::class);
        //$this->call(ProvidersTableSeeder::class);
        $this->call(RequestsTableSeeder::class);        
        $this->call(ServicesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        
        switch(DB::getDriverName()) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
        }
    }
}
