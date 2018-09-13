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

        // Scopes = 119. Permissions = 125.
        /*
         *  ADDRESS (1-5)
         *  ALLOCATIONS (6-7)
         *  APPS (8-12)
         *  ASSETS (13-14)
         *  CARRIERS (15-19)
         *  COMPANIES (20-24)
         *  COMPANYJOBS (25-29)
         *  CONDITIONS (30-34)
         *  CONTENTS (35-39)
         *  DEVICES (40-44)
         *  DEVICETYPES (45-49)
         *  DEVICEVARIATIONS (50-54)
         *  IMAGES (55-59)
         *  MODIFICATIONS (60-64)
         *  ORDERS (65-69)
         *  PACKAGES (70-75)
         *  PRESET (76-80)
         *  REQUEST (81-85)
         *  SERVICES (86-90)
         *  USERS (91-97)
         *  JOBS (98)
         *  ROLES (99-103)
         *  PERMISSIONS (104-108)
         *  SCOPES (109-113)
         *  GLOBALSETTINGS (114-118)
         *  SPECIAL CASES. (119:manage_devices, 120:manage_presets, 121:manage_services, 122:manage_employees, 123:manage_companies, 124:manage_own_company)
         *  DESKPRO (125)
         */

        $i = 1;
        while ($i < 118) {

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
        // MANAGE DEVICES -> 119
        $dataManageDevices = [
            [
                'scope_id' => 40,
                'permission_id' => 119
            ],
            [
                'scope_id' => 42,
                'permission_id' => 119
            ],
            [
                'scope_id' => 43,
                'permission_id' => 119
            ],
            [
                'scope_id' => 44,
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
                'scope_id' => 60,
                'permission_id' => 119
            ],
            [
                'scope_id' => 62,
                'permission_id' => 119
            ]
        ];

        $this->loadTable($dataManageDevices);

        // MANAGE PRESETS -> 120
        $dataManagePresets = [
            [
                'scope_id' => 76,
                'permission_id' => 120
            ],
            [
                'scope_id' => 78,
                'permission_id' => 120
            ],
            [
                'scope_id' => 79,
                'permission_id' => 120
            ],
            [
                'scope_id' => 80,
                'permission_id' => 120
            ],
            [
                'scope_id' => 40,
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
                'scope_id' => 60,
                'permission_id' => 120
            ]
        ];

        $this->loadTable($dataManagePresets);

        // MANAGE SERVICES -> 121
        $dataManageServices = [
            [
                'scope_id' => 86,
                'permission_id' => 121
            ],
            [
                'scope_id' => 88,
                'permission_id' => 121
            ],
            [
                'scope_id' => 89,
                'permission_id' => 121
            ],
            [
                'scope_id' => 90,
                'permission_id' => 121
            ],
            [
                'scope_id' => 40,
                'permission_id' => 121
            ],
            [
                'scope_id' => 15,
                'permission_id' => 121
            ],
            [
                'scope_id' => 20,
                'permission_id' => 121
            ],
            [
                'scope_id' => 60,
                'permission_id' => 121
            ]
        ];

        $this->loadTable($dataManageServices);

        // MANAGE EMPLOYEES -> 122
        $dataManageEmployees = [
            [
                'scope_id' => 91,
                'permission_id' => 122
            ],
            [
                'scope_id' => 95,
                'permission_id' => 122
            ],
            [
                'scope_id' => 96,
                'permission_id' => 122
            ],
            [
                'scope_id' => 97,
                'permission_id' => 122
            ],
            [
                'scope_id' => 20,
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

        $this->loadTable($dataManageEmployees);

        // MANAGE COMPANIES -> 123
        $dataManageCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 123
            ],
            [
                'scope_id' => 22,
                'permission_id' => 123
            ],
            [
                'scope_id' => 23,
                'permission_id' => 123
            ],
            [
                'scope_id' => 24,
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

        $this->loadTable($dataManageCompanies);

        // MANAGE OWN COMPANY -> 124 (Can't Create or Delete a Company.)
        $dataManageOwnCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 124
            ],
            [
                'scope_id' => 23,
                'permission_id' => 124
            ],
            [
                'scope_id' => 1,
                'permission_id' => 124
            ],
            [
                'scope_id' => 3,
                'permission_id' => 124
            ],
            [
                'scope_id' => 4,
                'permission_id' => 124
            ],
            [
                'scope_id' => 5,
                'permission_id' => 124
            ]
        ];

        $this->loadTable($dataManageOwnCompanies);

        // DESKPRO -> 125
        $dataDeskPro = [
            [
                'scope_id' => 119,
                'permission_id' => 125
            ]
        ];

        $this->loadTable($dataDeskPro);

        // GET ALL LOCATIONS -> 126
        $dataLocation = [
            [
                'scope_id' => 120,
                'permission_id' => 126
            ]
        ];

        $this->loadTable($dataLocation);
    }
}
