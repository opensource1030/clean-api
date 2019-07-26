<?php

use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

class UsersTableSeeder extends BaseTableSeeder
{
    protected $table = 'users';

    public function run()
    {
        $this->deleteTable();

        $client = new Client;

        try
        {
            $address = $client->get( Config::get('seeders.mockaroo_url') . '/' . Config::get('seeders.mockaroo_codes.users.code'), 
                [ 'headers' => [ 'Content-Type' => 'application/json' ], 'query' => [ 'count' => Config::get('seeders.mockaroo_codes.users.numitems'), 'key' => Config::get('seeders.mockaroo_key') ] ]
            );

            $rows = json_decode( $address->getBody()->getContents(), true );

            foreach( array_chunk( $rows , Config::get('seeders.mockaroo_codes.users.itemsPerPage') ) as $key => $values )
            {
                $this->loadTable( $values );
            }
        }
        catch( ClientErrorResponseException $exception )
        {
            $responseBody = $exception->getResponse()->getBody( TRUE );

            Log::debug('ERROR UsersTableSeeder: '. print_r( $responseBody, true ));
        }
    }
}
