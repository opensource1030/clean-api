<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class PasswordGrantTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     */
    public function testRespondToAccessTokenRequest() {
        //if (!$this->invokeMethod($scopes,'testIfthisUserHasTheCorrectScope', $user->getIdentifier())) {
    }


    public function testIfThisUserHasTheCorrectScope(){
    	
    	$user = factory(\WA\DataStore\User\User::class)->create();
    	$userId = $user->id;
        $role = factory(\WA\DataStore\Role\Role::class)->create();
        
        $permission1 = factory(\WA\DataStore\Permission\Permission::class)->create();
        $permission2 = factory(\WA\DataStore\Permission\Permission::class)->create();
        $scope = factory(\WA\DataStore\Scope\Scope::class)->create();
        $scope2 = factory(\WA\DataStore\Scope\Scope::class)->create();

        $user->roles()->sync([$role->id]);
        $role ->perms()->sync([$permission1->id,$permission2->id]);
        $permission1 -> scopes()->sync([$scope->id]);
        $permission2 -> scopes()->sync([$scope->id]);

        $var = $this->invokeMethod($scopes,'thisUserHasTheCorrectScope', array($scope, $userId)); 
        $var2 = $this->invokeMethod($scopes,'thisUserHasTheCorrectScope', array($scope2, $userId)); 
         $this->assertTrue($var);
         $this->assertFalse($var2);



      }
	    /* Call protected/private method of a class.
	 *
	 * @param object &$object    Instantiated object that we will run method on.
	 * @param string $methodName Method name to call
	 * @param array  $parameters Array of parameters to pass into method.
	 *
	 * @return mixed Method return.
	 */
	public function invokeMethod(&$object, $methodName, array $parameters = array())
	{
	    $reflection = new \ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);

	    return $method->invokeArgs($object, $parameters);
	}

}
