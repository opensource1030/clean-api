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

        // Scopes = 117. Permissions = 123.
        /*
         *  ADDRESS (1-5)
         *  ALLOCATIONS (6-7)
         *  APPS (8-12)
         *  ASSETS (13-14)
         *  CARRIERS (15-19)
         *  COMPANIES (20-24)
         *  COMPANYJOBS (25-28)
         *  CONDITIONS (29-33)
         *  CONTENTS (34-38)
         *  DEVICES (39-43)
         *  DEVICETYPES (44-48)
         *  DEVICEVARIATIONS (49-53)
         *  IMAGES (54-58)
         *  MODIFICATIONS (59-63)
         *  ORDERS (64-68)
         *  PACKAGES (69-74)
         *  PRESET (75-79)
         *  REQUEST (80-84)
         *  SERVICES (85-89)
         *  USERS (90-96)
         *  JOBS (97)
         *  ROLES (98-102)
         *  PERMISSIONS (103-107)
         *  SCOPES (108-112)
         *  GLOBALSETTINGS (113-117)
         *  SPECIAL CASES. (118:manage_devices, 119:manage_presets, 120:manage_services, 121:manage_employees, 122:manage_companies, 123:manage_own_company)
         */

        $i = 1;
        while ($i < 117) {

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
        // MANAGE DEVICES -> 118
        $dataManageDevices = [
            [
                'scope_id' => 39,
                'permission_id' => 118
            ],
            [
                'scope_id' => 41,
                'permission_id' => 118
            ],
            [
                'scope_id' => 42,
                'permission_id' => 118
            ],
            [
                'scope_id' => 43,
                'permission_id' => 118
            ],
            [
                'scope_id' => 15,
                'permission_id' => 118
            ],
            [
                'scope_id' => 20,
                'permission_id' => 118
            ],
            [
                'scope_id' => 59,
                'permission_id' => 118
            ],
            [
                'scope_id' => 61,
                'permission_id' => 118
            ]
        ];

        $this->loadTable($dataManageDevices);

        // MANAGE PRESETS -> 119
        $dataManagePresets = [
            [
                'scope_id' => 75,
                'permission_id' => 119
            ],
            [
                'scope_id' => 77,
                'permission_id' => 119
            ],
            [
                'scope_id' => 78,
                'permission_id' => 119
            ],
            [
                'scope_id' => 79,
                'permission_id' => 119
            ],
            [
                'scope_id' => 39,
                'permission_id' => 119
            ],
            [
                'scope_id' => 15,
                'permission_id' => 119
            ],
            [
                'scope_id' => 20,
                'permission_id' => 119
            ],
            [
                'scope_id' => 59,
                'permission_id' => 119
            ]
        ];

        $this->loadTable($dataManagePresets);

        // MANAGE SERVICES -> 120
        $dataManageServices = [
            [
                'scope_id' => 85,
                'permission_id' => 120
            ],
            [
                'scope_id' => 87,
                'permission_id' => 120
            ],
            [
                'scope_id' => 88,
                'permission_id' => 120
            ],
            [
                'scope_id' => 89,
                'permission_id' => 120
            ],
            [
                'scope_id' => 39,
                'permission_id' => 120
            ],
            [
                'scope_id' => 15,
                'permission_id' => 120
            ],
            [
                'scope_id' => 20,
                'permission_id' => 120
            ],
            [
                'scope_id' => 59,
                'permission_id' => 120
            ]
        ];

        $this->loadTable($dataManageServices);

        // MANAGE EMPLOYEES -> 121
        $dataManageEmployees = [
            [
                'scope_id' => 90,
                'permission_id' => 121
            ],
            [
                'scope_id' => 94,
                'permission_id' => 121
            ],
            [
                'scope_id' => 95,
                'permission_id' => 121
            ],
            [
                'scope_id' => 96,
                'permission_id' => 121
            ],
            [
                'scope_id' => 20,
                'permission_id' => 121
            ],
            [
                'scope_id' => 1,
                'permission_id' => 121
            ],
            [
                'scope_id' => 3,
                'permission_id' => 121
            ],
            [
                'scope_id' => 4,
                'permission_id' => 121
            ],
            [
                'scope_id' => 5,
                'permission_id' => 121
            ]
        ];

        $this->loadTable($dataManageEmployees);

        // MANAGE COMPANIES -> 122
        $dataManageCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 122
            ],
            [
                'scope_id' => 22,
                'permission_id' => 122
            ],
            [
                'scope_id' => 23,
                'permission_id' => 122
            ],
            [
                'scope_id' => 24,
                'permission_id' => 122
            ],
            [
                'scope_id' => 1,
                'permission_id' => 122
            ],
            [
                'scope_id' => 3,
                'permission_id' => 122
            ],
            [
                'scope_id' => 4,
                'permission_id' => 122
            ],
            [
                'scope_id' => 5,
                'permission_id' => 122
            ]
        ];

        $this->loadTable($dataManageCompanies);

        // MANAGE OWN COMPANY -> 123 (Can't Create or Delete a Company.)
        $dataManageOwnCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 123
            ],
            [
                'scope_id' => 23,
                'permission_id' => 123
            ],
            [
                'scope_id' => 1,
                'permission_id' => 123
            ],
            [
                'scope_id' => 3,
                'permission_id' => 123
            ],
            [
                'scope_id' => 4,
                'permission_id' => 123
            ],
            [
                'scope_id' => 5,
                'permission_id' => 123
            ]
        ];

        $this->loadTable($dataManageOwnCompanies);
    }
}
