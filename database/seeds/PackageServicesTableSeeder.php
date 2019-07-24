<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

/**
 * PackageServicesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class PackageServicesTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_services';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();
        
        $client = new Client;

        try
        {
            $address = $client->get( Config::get('seeders.mockaroo_url') . '/' . Config::get('seeders.mockaroo_codes.package_services.code'), 
                [ 'headers' => [ 'Content-Type' => 'application/json' ], 'query' => [ 'count' => Config::get('seeders.mockaroo_codes.package_services.numitems'), 'key' => Config::get('seeders.mockaroo_key') ] ]
            );

            $rows = json_decode( $address->getBody()->getContents(), true );

            foreach( array_chunk( $rows , Config::get('seeders.mockaroo_codes.package_services.itemsPerPage') ) as $key => $values )
            {
                $this->loadTable( $values );
            }
        }
        catch( ClientErrorResponseException $exception )
        {
            $responseBody = $exception->getResponse()->getBody( TRUE );

            Log::debug('ERROR PackageServicesTableSeeder: '. print_r( $responseBody, true ));
        }
    }
}
