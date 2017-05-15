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

        $i = 1;
        while ($i < 95) {
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
