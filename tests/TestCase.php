<?php

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    protected $baseUrl;

    protected $mainCompany, $mainUserSuperAdmin, $roleSuperAdmin, $mainUserAdmin, $roleAdmin, $mainUser, $roleUser;

    /*
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $this->baseUrl = 'http://'.getenv('API_DOMAIN');

        return $app;
    }

    public function setUp(){
        parent::setUp();

        $this->mainCompany = factory(\WA\DataStore\Company\Company::class)->create()->id;
        
        $this->mainUserSuperAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleSuperAdmin = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'superadmin', 'name' => 'superadmin']);
        $this->mainUserSuperAdmin->roles()->sync([$this->roleSuperAdmin->id]);

        $this->mainUserAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleAdmin = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'admin', 'name' => 'admin']);
        $this->mainUserAdmin->roles()->sync([$this->roleAdmin->id]);

        $this->mainUser = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany]);
        $this->roleUser = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'user', 'name' => 'user']);
        $this->mainUser->roles()->sync([$this->roleUser->id]);

        /*
         * Use one of the lines below to login using any of the users above.
         */
        $this->be($this->mainUserSuperAdmin);
        //$this->be($mainUserAdmin);
        //$this->be($mainUser);
    }
}
