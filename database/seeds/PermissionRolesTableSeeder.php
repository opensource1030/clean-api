<?php

/**
 * PermissionRoleTableSeeder - Insert info into database.
 *
 * @author   Marcio de Rezende
 */
class PermissionRolesTableSeeder extends BaseTableSeeder
{
    protected $table = 'permission_role';

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
                    'permission_id' => $i,
                    'role_id' => 1 // admin
                ]
            ];

            $this->loadTable($data);
            $i++;
        }

        $j = 1;
        while ($j < 95) {
            $data = [
                [
                    'permission_id' => $j,
                    'role_id' => 2
                ]
            ];

            $this->loadTable($data);
            $j++;
        }

        $dataUser = [
            [
                'permission_id' => 1,
                'role_id' => 3,
            ],
            [
                'permission_id' => 2,
                'role_id' => 3,
            ],
            [
                'permission_id' => 6,
                'role_id' => 3,
            ],
            [
                'permission_id' => 7,
                'role_id' => 3,
            ],
            [
                'permission_id' => 11,
                'role_id' => 3,
            ],
            [
                'permission_id' => 12,
                'role_id' => 3,
            ],
            [
                'permission_id' => 16,
                'role_id' => 3,
            ],
            [
                'permission_id' => 17,
                'role_id' => 3,
            ],
            [
                'permission_id' => 20,
                'role_id' => 3,
            ],
            [
                'permission_id' => 21,
                'role_id' => 3,
            ],
            [
                'permission_id' => 22,
                'role_id' => 3,
            ],
            [
                'permission_id' => 23,
                'role_id' => 3,
            ],
            [
                'permission_id' => 28,
                'role_id' => 3,
            ],
            [
                'permission_id' => 29,
                'role_id' => 3,
            ],
            [
                'permission_id' => 30,
                'role_id' => 3,
            ],
            [
                'permission_id' => 31,
                'role_id' => 3,
            ],
            [
                'permission_id' => 35,
                'role_id' => 3,
            ],
            [
                'permission_id' => 36,
                'role_id' => 3,
            ],
            [
                'permission_id' => 40,
                'role_id' => 3,
            ],
            [
                'permission_id' => 41,
                'role_id' => 3,
            ],
            [
                'permission_id' => 45,
                'role_id' => 3,
            ],
            [
                'permission_id' => 46,
                'role_id' => 3,
            ],
            [
                'permission_id' => 50,
                'role_id' => 3,
            ],
            [
                'permission_id' => 51,
                'role_id' => 3,
            ],
            [
                'permission_id' => 52,
                'role_id' => 3,
            ],
            [
                'permission_id' => 56,
                'role_id' => 3,
            ],
            [
                'permission_id' => 57,
                'role_id' => 3,
            ],
            [
                'permission_id' => 61,
                'role_id' => 3,
            ],
            [
                'permission_id' => 62,
                'role_id' => 3,
            ],
            [
                'permission_id' => 66,
                'role_id' => 3,
            ],
            [
                'permission_id' => 67,
                'role_id' => 3,
            ],
            [
                'permission_id' => 71,
                'role_id' => 3,
            ],
            [
                'permission_id' => 72,
                'role_id' => 3,
            ],
            [
                'permission_id' => 76,
                'role_id' => 3,
            ],
            [
                'permission_id' => 77,
                'role_id' => 3,
            ],
            [
                'permission_id' => 81,
                'role_id' => 3,
            ],
            [
                'permission_id' => 82,
                'role_id' => 3,
            ],
            [
                'permission_id' => 86,
                'role_id' => 3,
            ],
            [
                'permission_id' => 87,
                'role_id' => 3,
            ],
            [
                'permission_id' => 91,
                'role_id' => 3,
            ],
            [
                'permission_id' => 92,
                'role_id' => 3,
            ]
        ];

        $this->loadTable($dataUser);
    }
}
