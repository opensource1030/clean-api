<?php

class OAuthTableSeeder extends BaseTableSeeder
{
    protected $tableName = "oauth_clients";
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'id' => 'g73hhd8j3bhcuhdbbs88e4wd',
            'secret' => '786wndkd8iu4nn49ixjndfodsde33',
            'name' => 'Some App',
            'created_at' => '2016-08-09 14:14:00',
            'updated_at' => '0000-00-00 00:00:00'
        ];


        $this->loadTable($data);
    }
}
