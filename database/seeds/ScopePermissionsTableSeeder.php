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
        /*
         *  ADDRESS (1-5)
         *  ALLOCATIONS (6-7)
         *  APPS (8-12)
         *  ASSETS (13-14)
         *  CARRIERS (15-19)
         *  COMPANIES (20-24)
         *  CONDITIONS (25-29)
         *  CONTENTS (30-34)
         *  DEVICES (35-39)
         *  DEVICETYPES (40-44)
         *  DEVICEVARIATIONS (45-49)
         *  IMAGES (50-54)
         *  MODIFICATIONS (55-59)
         *  ORDERS (60-64)
         *  PACKAGES (65-70)
         *  PRESET (71-75)
         *  REQUEST (76-80)
         *  SERVICES (81-85)
         *  USERS (86-92)
         *  JOBS (93)
         *  ROLES (94-98)
         *  PERMISSIONS (99-103)
         *  SCOPES (104-108)
         *  GLOBALSETTINGS (109-113)
         *  SPECIAL CASES. (114:manage_devices, 115:manage_presets, 116:manage_services, 117:manage_employees, 118:manage_companies)
         */

        $i = 1;
        while ($i < 113) {

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
        // MANAGE DEVICES -> 114
        $dataManageDevices = [
            [
                'scope_id' => 35,
                'permission_id' => 114
            ],
            [
                'scope_id' => 37,
                'permission_id' => 114
            ],
            [
                'scope_id' => 38,
                'permission_id' => 114
            ],
            [
                'scope_id' => 39,
                'permission_id' => 114
            ],
            [
                'scope_id' => 15,
                'permission_id' => 114
            ],
            [
                'scope_id' => 20,
                'permission_id' => 114
            ],
            [
                'scope_id' => 55,
                'permission_id' => 114
            ],
            [
                'scope_id' => 57,
                'permission_id' => 114
            ]
        ];

        $this->loadTable($dataManageDevices);

        // MANAGE PRESETS -> 115
        $dataManagePresets = [
            [
                'scope_id' => 71,
                'permission_id' => 115
            ],
            [
                'scope_id' => 73,
                'permission_id' => 115
            ],
            [
                'scope_id' => 74,
                'permission_id' => 115
            ],
            [
                'scope_id' => 75,
                'permission_id' => 115
            ],
            [
                'scope_id' => 35,
                'permission_id' => 115
            ],
            [
                'scope_id' => 15,
                'permission_id' => 115
            ],
            [
                'scope_id' => 20,
                'permission_id' => 115
            ],
            [
                'scope_id' => 55,
                'permission_id' => 115
            ]
        ];

        $this->loadTable($dataManagePresets);

        // MANAGE SERVICES -> 116
        $dataManageServices = [
            [
                'scope_id' => 81,
                'permission_id' => 116
            ],
            [
                'scope_id' => 83,
                'permission_id' => 116
            ],
            [
                'scope_id' => 84,
                'permission_id' => 116
            ],
            [
                'scope_id' => 85,
                'permission_id' => 116
            ],
            [
                'scope_id' => 35,
                'permission_id' => 116
            ],
            [
                'scope_id' => 15,
                'permission_id' => 116
            ],
            [
                'scope_id' => 20,
                'permission_id' => 116
            ],
            [
                'scope_id' => 55,
                'permission_id' => 116
            ]
        ];

        $this->loadTable($dataManageServices);

        // MANAGE EMPLOYEES -> 117
        $dataManageEmployees = [
            [
                'scope_id' => 86,
                'permission_id' => 117
            ],
            [
                'scope_id' => 90,
                'permission_id' => 117
            ],
            [
                'scope_id' => 91,
                'permission_id' => 117
            ],
            [
                'scope_id' => 92,
                'permission_id' => 117
            ],
            [
                'scope_id' => 20,
                'permission_id' => 117
            ],
            [
                'scope_id' => 1,
                'permission_id' => 117
            ],
            [
                'scope_id' => 3,
                'permission_id' => 117
            ],
            [
                'scope_id' => 4,
                'permission_id' => 117
            ],
            [
                'scope_id' => 5,
                'permission_id' => 117
            ]
        ];

        $this->loadTable($dataManageEmployees);

        // MANAGE COMPANIES -> 118
        $dataManageCompanies = [
            [
                'scope_id' => 20,
                'permission_id' => 118
            ],
            [
                'scope_id' => 22,
                'permission_id' => 118
            ],
            [
                'scope_id' => 23,
                'permission_id' => 118
            ],
            [
                'scope_id' => 24,
                'permission_id' => 118
            ],
            [
                'scope_id' => 1,
                'permission_id' => 118
            ],
            [
                'scope_id' => 3,
                'permission_id' => 118
            ],
            [
                'scope_id' => 4,
                'permission_id' => 118
            ],
            [
                'scope_id' => 5,
                'permission_id' => 118
            ]
        ];

        $this->loadTable($dataManageCompanies);
    }
}
