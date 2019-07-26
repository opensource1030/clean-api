<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class PackageAddressTableSeeder extends BaseTableSeeder
{
    protected $table = 'package_address';

    public function run()
    {
        $this->deleteTable();

        $client = new Client;

        try
        {
            $address = $client->get( Config::get('seeders.mockaroo_url') . '/' . Config::get('seeders.mockaroo_codes.package_address.code'), 
                [ 'headers' => [ 'Content-Type' => 'application/json' ], 'query' => [ 'count' => Config::get('seeders.mockaroo_codes.package_address.numitems'), 'key' => Config::get('seeders.mockaroo_key') ] ]
            );

            $rows = json_decode( $address->getBody()->getContents(), true );

            foreach( array_chunk( $rows , Config::get('seeders.mockaroo_codes.package_address.itemsPerPage') ) as $key => $values )
            {
                $this->loadTable( $values );
            }
        }
        catch( ClientErrorResponseException $exception )
        {
            $responseBody = $exception->getResponse()->getBody( TRUE );

            Log::debug('ERROR PackageAddressTableSeeder: '. print_r( $responseBody, true ));
        }
    }
}