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
        while ($i < 109) {

            $data = [
                [
                    'scope_id' => $i,
                    'permission_id' => $i
                ]
            ];

            $this->loadTable($data);
            $i++;
        }

        // SPECIAL CASES.
        // MANAGE DEVICES -> 109
        $dataManageDevices = [
            [
                'scope_id' => 35,
                'permission_id' => 109
            ],
            [
                'scope_id' => 37,
                'permission_id' => 109
            ],
            [
                'scope_id' => 38,
                'permission_id' => 109
            ],
            [
                'scope_id' => 39,
                'permission_id' => 109
            ],
            [
                'scope_id' => 15,
                'permission_id' => 109
            ],
            [
                'scope_id' => 20,
                'permission_id' => 109
            ],
            [
                'scope_id' => 55,
                'permission_id' => 109
            ],
            [
                'scope_id' => 57,
                'permission_id' => 109
            ]
        ];

        $this->loadTable($dataManageDevices);

        // MANAGE PRESETS -> 110
        $dataManagePresets = [
            [
                'scope_id' => 71,
                'permission_id' => 110
            ],
            [
                'scope_id' => 73,
                'permission_id' => 110
            ],
            [
                'scope_id' => 74,
                'permission_id' => 110
            ],
            [
                'scope_id' => 75,
                'permission_id' => 110
            ],
            [
                'scope_id' => 35,
                'permission_id' => 110
            ],
            [
                'scope_id' => 15,
                'permission_id' => 110
            ],
            [
                'scope_id' => 20,
                'permission_id' => 110
            ],
            [
                'scope_id' => 55,
                'permission_id' => 110
            ]
        ];

        $this->loadTable($dataManagePresets);

        // MANAGE SERVICES -> 111
        $dataManageServices = [
            [
                'scope_id' => 81,
                'permission_id' => 111
            ],
            [
                'scope_id' => 83,
                'permission_id' => 111
            ],
            [
                'scope_id' => 84,
                'permission_id' => 111
            ],
            [
                'scope_id' => 85,
                'permission_id' => 111
            ],
            [
                'scope_id' => 35,
                'permission_id' => 111
            ],
            [
                'scope_id' => 15,
                'permission_id' => 111
            ],
            [
                'scope_id' => 20,
                'permission_id' => 111
            ],
            [
                'scope_id' => 55,
                'permission_id' => 111
            ]
        ];

        $this->loadTable($dataManageServices);

        // MANAGE EMPLOYEES -> 112
        $dataManageEmployees = [
            [
                'scope_id' => 86,
                'permission_id' => 112
            ],
            [
                'scope_id' => 90,
                'permission_id' => 112
            ],
            [
                'scope_id' => 91,
                'permission_id' => 112
            ],
            [
                'scope_id' => 92,
                'permission_id' => 112
            ],
            [
                'scope_id' => 20,
                'permission_id' => 112
            ],
            [
                'scope_id' => 1,
                'permission_id' => 112
            ],
            [
                'scope_id' => 3,
                'permission_id' => 112
            ],
            [
                'scope_id' => 4,
                'permission_id' => 112
            ],
            [
                'scope_id' => 5,
                'permission_id' => 112
            ]
        ];

        $this->loadTable($dataManageEmployees);

        // MANAGE COMPANIES -> 113
        $dataManageCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 113
            ],
            [
                'scope_id' => 22,
                'permission_id' => 113
            ],
            [
                'scope_id' => 23,
                'permission_id' => 113
            ],
            [
                'scope_id' => 24,
                'permission_id' => 113
            ],
            [
                'scope_id' => 1,
                'permission_id' => 113
            ],
            [
                'scope_id' => 3,
                'permission_id' => 113
            ],
            [
                'scope_id' => 4,
                'permission_id' => 113
            ],
            [
                'scope_id' => 5,
                'permission_id' => 113
            ]
        ];

        $this->loadTable($dataManageCompanies);
    }
}
