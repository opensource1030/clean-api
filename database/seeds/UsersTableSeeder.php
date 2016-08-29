<?php


class UsersTableSeeder extends BaseTableSeeder
{
    protected $table = 'users';

    public function run()
    {
        $this->deleteTable();

        $data = [
        	'identification' => uniqid('WA-'),
        	'uuid' => '123456789',
        	'email' => 'sirion@developers.com',
        	'supervisorEmail' => 'admin@siriondev.com',
        	'password' => bcrypt('user'),
        	'confirmation_code' => md5(uniqid(mt_rand(), true)),
        	'confirmed' => 1,
        	'firstName' => 'Sirion',
        	'lastName' => 'Developers',
        	'username' => 'sirion',
        	'defaultLang' => 'en',
	        'supervisorId' => 4,
    	    'approverId' => 3,
        	'defaultLocationId' => 'location',
	        'companyId' => 1
	    ];

	    $this->loadTable($data);

        factory(\WA\DataStore\User\User::class, 40)->create();
    }

}
