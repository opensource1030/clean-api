<?php

abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
    use \Laravel\Lumen\Testing\DatabaseMigrations;

    protected $baseUrl;

    protected $idLogged, $mainCompany, $mainUserAdmin, $roleAdmin, $mainUserWTA, $roleWTA, $mainUser, $roleUser, $configuration = 1;

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

        $this->mainCompany = factory(\WA\DataStore\Company\Company::class)->create();
        
        $this->mainUserAdmin = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany->id]);
        $this->roleAdmin = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'admin', 'name' => 'admin']);
        $this->mainUserAdmin->roles()->sync([$this->roleAdmin->id]);

        $this->mainUserWTA = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany->id]);
        $this->roleWTA = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'wta', 'name' => 'wta']);
        $this->mainUserWTA->roles()->sync([$this->roleWTA->id]);

        $this->mainUser = factory(\WA\DataStore\User\User::class)->create(['companyId' => $this->mainCompany->id]);
        $this->roleUser = factory(\WA\DataStore\Role\Role::class)->create(['display_name' => 'user', 'name' => 'user']);
        $this->mainUser->roles()->sync([$this->roleUser->id]);

        /*
         * Use one of the lines below to login using any of the users above.
         */

        if($this->configuration == 1) {
            $this->be($this->mainUserAdmin);
            $this->idLogged = $this->mainUserAdmin->id;
        } else if($this->configuration == 2) {
            $this->be($this->mainUserWTA);
            $this->idLogged = $this->mainUserWTA->id;
        } else if($this->configuration == 3) {
            $this->be($this->mainUser);
            $this->idLogged = $this->mainUser->id;
        } else {}
    }

    public function run(\PHPUnit\Framework\TestResult $result = NULL): \PHPUnit\Framework\TestResult {

/*
        if ($result === NULL) {
            $result = $this->createResult();
        }
*/
        // $this->configuration = 1 with SuperAdmin. Use CONSTANT.
        // $result->run($this);
        $result = parent::run($result);
        $this->configuration++;

        //$result->run($this);
        $result = parent::run($result);
        $this->configuration++;

        //$result->run($this);
        $result = parent::run($result);
        //$this->configuration = 1;

        return $result;
    }
}
