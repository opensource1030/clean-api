<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

/**
 * PackageDevicesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageDevicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_devices';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $client = new Client;

        try
        {
            $address = $client->get( Config::get('seeders.mockaroo_url') . '/' . Config::get('seeders.mockaroo_codes.package_devices.code'), 
                [ 'headers' => [ 'Content-Type' => 'application/json' ], 'query' => [ 'count' => Config::get('seeders.mockaroo_codes.package_devices.numitems'), 'key' => Config::get('seeders.mockaroo_key') ] ]
            );

            $rows = json_decode( $address->getBody()->getContents(), true );

            foreach( array_chunk( $rows , Config::get('seeders.mockaroo_codes.package_devices.itemsPerPage') ) as $key => $values )
            {
                $this->loadTable( $values );
            }
        }
        catch( ClientErrorResponseException $exception )
        {
            $responseBody = $exception->getResponse()->getBody( TRUE );

            Log::debug('ERROR packagesTableSeeder: '. print_r( $responseBody, true ));
        }
    }
}
