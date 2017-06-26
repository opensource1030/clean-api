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

        // ADMIN: ALL PERMISSIONS.

        $i = 1;
        while ($i < 118) {
            $data = [
                [
                    'permission_id' => $i,
                    'role_id' => 1 // ADMIN
                ]
            ];

            $this->loadTable($data);
            $i++;
        }

        // WTA: SOME PERMISSIONS (Asociated to the company of the admin)
        // note: I've commented categoryapps, conditionfields and conditionoperators from routes.

        $j = 1;
        while ($j < 118) {
            // NOT assign the permissions listed below.
            if(    $j != 22 // create_company
                && $j != 24 // delete_company
                && $j != 29 // delete_condition
                && $j != 50 // get_images
                && $j != 54 // delete_image
                && $j != 100 // create_permission
                && $j != 101 // update_permission
                && $j != 102 // delete_permission
                && $j != 103 // get_scopes
                && $j != 104 // get_scope
                && $j != 105 // create_scope
                && $j != 106 // update_scope
                && $j != 107 // delete_scope
            ) {
                $data = [
                    [
                        'permission_id' => $j,
                        'role_id' => 2 // WTA
                    ]
                ];

                $this->loadTable($data);    
            }
            
            $j++;
        }

        // USER: A FEW PERMISSIONS.
        // Assign the permissions listed below.
        $dataUser = [
            [
                'permission_id' => 1, // get_addresses
                'role_id' => 3,
            ],
            [
                'permission_id' => 8, // get_apps
                'role_id' => 3,
            ],
            [
                'permission_id' => 45, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 46, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 51, // get_image
                'role_id' => 3,
            ],
            [
                'permission_id' => 52, // get_image_info
                'role_id' => 3,
            ],
            [
                'permission_id' => 60, // get_orders
                'role_id' => 3,
            ],
            [
                'permission_id' => 61, // get_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 62, // create_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 63, // update_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 66, // get_packages_foruser
                'role_id' => 3,
            ],
            [
                'permission_id' => 67, // get_package
                'role_id' => 3,
            ],
            [
                'permission_id' => 81, // get_services
                'role_id' => 3,
            ],
            [
                'permission_id' => 82, // get_service
                'role_id' => 3,
            ],
            [
                'permission_id' => 88, // get_user_me
                'role_id' => 3,
            ],
            [
                'permission_id' => 91, // update_user
                'role_id' => 3,
            ]
        ];

        $this->loadTable($dataUser);
    }
}
