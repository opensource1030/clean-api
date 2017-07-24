<?php

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    protected $baseUrl;

    protected $mainCompany, $mainUserAdmin, $roleAdmin, $mainUserWTA, $roleWTA, $mainUser, $roleUser;

    /*
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $this->baseUrl = 'http://'.getenv('API_DOMAIN');

        //$app->make(Kernel::class)->bootstrap();
        //$app['config']->set('database.default','sqlite');
        //$app['config']->set('database.connections.sqlite.database', ':memory:');

        return $app;

    }

    public function setUp(){
        parent::setUp();

        $this->mainCompany = factory(\WA\DataStore\Company\Company::class)->create()->id;
        
        $this->mainUserAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleAdmin = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'admin', 'name' => 'admin']);
        $this->mainUserAdmin->roles()->sync([$this->roleAdmin->id]);

        $this->mainUserWTA = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleWTA = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'wta', 'name' => 'wta']);
        $this->mainUserWTA->roles()->sync([$this->roleWTA->id]);

        $this->mainUser = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleUser = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'user', 'name' => 'user']);
        $this->mainUser->roles()->sync([$this->roleUser->id]);

        /*
         * Use one of the lines below to login using any of the users above.
         */
        $this->be($this->mainUserAdmin);
        //$this->be($this->mainUserWTA);
        //$this->be($this->mainUser);
    }
}
