<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        switch (DB::getDriverName()) {
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
        $this->call(CarrierImagesTableSeeder::class);
        $this->call(CarriersTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(CompanyDomainsTableSeeder::class);
        $this->call(CompanySaml2TableSeeder::class);
        $this->call(ConditionFieldsTableSeeder::class);
        $this->call(ConditionOperatorsTableSeeder::class);
        $this->call(ConditionsTableSeeder::class);
        $this->call(DeviceCarriersTableSeeder::class);
        $this->call(DeviceCompaniesTableSeeder::class);
        $this->call(DeviceImagesTableSeeder::class);
        $this->call(DeviceModificationsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(ImagesTableSeeder::class);
        $this->call(LocationsTableSeeder::class);
        $this->call(ModificationsTableSeeder::class);
        //$this->call(OAuthTableSeeder::class);
        $this->call(OauthClientsTableSeeder::class);

        $this->call(OrderServiceItemsTableSeeder::class);
        $this->call(OrderAppsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(PackageAppsTableSeeder::class);
        $this->call(PackageConditionsTableSeeder::class);
        $this->call(PackageDevicesTableSeeder::class);
        $this->call(PackageServicesTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        //$this->call(PagesTableSeeder::class);
        $this->call(PricesTableSeeder::class);
        //$this->call(ProvidersTableSeeder::class);
        $this->call(RequestsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(ServiceItemsTableSeeder::class);
        $this->call(ServicesTableSeeder::class);
        $this->call(UdlValuesTableSeeder::class);
        $this->call(UdlsTableSeeder::class);
        $this->call(UserAssetsTableSeeder::class);
        $this->call(UserDevicesTableSeeder::class);
        $this->call(UserUdlsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(PresetTableSeeder::class);
        $this->call(PresetDeviceTableSeeder::class);
        $this->call(PresetImageTableSeeder::class);

        switch (DB::getDriverName()) {
            case 'mysql':
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                break;
            case 'sqlite':
                DB::statement('PRAGMA foreign_keys = ON');
                break;
        }
    }
}
