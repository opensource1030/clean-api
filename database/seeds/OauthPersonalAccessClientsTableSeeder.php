<?php

/**
 * OauthPersonalAccessClientsTableSeeder - Insert the id of the Personal Access stored in oauth_clients
 *
 * @author   Agustí Dosaiguas
 */
class OauthPersonalAccessClientsTableSeeder extends BaseTableSeeder
{
    protected $table = 'oauth_personal_access_clients';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        $data = [
            [
                'id' => null,
                'client_id' => 1,
            ],
        ];

        $this->loadTable($data);
    }
}
