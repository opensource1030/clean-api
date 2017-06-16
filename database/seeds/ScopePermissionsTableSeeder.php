<?php

/**
 * ScopePermissionTableSeeder - Insert info into database.
 *
 * @author   Marcio de Rezende
 */
class ScopePermissionsTableSeeder extends BaseTableSeeder
{
    protected $table = 'scope_permission';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        // Scopes = 108. Permissions = 108.

        $i = 1;
        while ($i < 112) {

            $data = [
                [
                    'scope_id' => $i,
                    'permission_id' => $i
                ]
            ];

            $this->loadTable($data);
            $i++;
        }
    }
}
