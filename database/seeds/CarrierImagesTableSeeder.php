<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

/**
 * CarrierImagesTableSeeder - Insert info into database.
 *
 * @author   Agustí Dosaiguas
 */
class CarrierImagesTableSeeder extends BaseTableSeeder
{
    protected $table = 'carrier_images';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();
        
        $client = new Client;

        try
        {
            $address = $client->get( Config::get('seeders.mockaroo_url') . '/' . Config::get('seeders.mockaroo_codes.carrier_images.code'), 
                [ 'headers' => [ 'Content-Type' => 'application/json' ], 'query' => [ 'count' => Config::get('seeders.mockaroo_codes.carrier_images.numitems'), 'key' => Config::get('seeders.mockaroo_key') ] ]
            );

            $rows = json_decode( $address->getBody()->getContents(), true );

            foreach( array_chunk( $rows , Config::get('seeders.mockaroo_codes.carrier_images.itemsPerPage') ) as $key => $values )
            {
                $this->loadTable( $values );
            }
        }
        catch( ClientErrorResponseException $exception )
        {
            $responseBody = $exception->getResponse()->getBody( TRUE );

            Log::debug('ERROR CarriersTableSeeder: '. print_r( $responseBody, true ));
        }
    }
}
