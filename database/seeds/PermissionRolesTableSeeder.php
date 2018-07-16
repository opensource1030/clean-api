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

        while ($i < 125) {
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
        while ($j < 125) {
            // NOT assign the permissions listed below.
            if(    $j != 22 // create_company
                && $j != 24 // delete_company
                && $j != 34 // delete_condition
                && $j != 55 // get_images
                && $j != 59 // delete_image
                && $j != 105 // create_permission
                && $j != 106 // update_permission
                && $j != 107 // delete_permission
                && $j != 108 // get_scopes
                && $j != 109 // get_scope
                && $j != 110 // create_scope
                && $j != 111 // update_scope
                && $j != 112 // delete_scope
                && $j != 123 // manage_companies
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
                'permission_id' => 50, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 51, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 56, // get_image
                'role_id' => 3,
            ],
            [
                'permission_id' => 57, // get_image_info
                'role_id' => 3,
            ],
            [
                'permission_id' => 65, // get_orders
                'role_id' => 3,
            ],
            [
                'permission_id' => 66, // get_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 67, // create_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 68, // update_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 71, // get_packages_foruser
                'role_id' => 3,
            ],
            [
                'permission_id' => 72, // get_package
                'role_id' => 3,
            ],
            [
                'permission_id' => 86, // get_services
                'role_id' => 3,
            ],
            [
                'permission_id' => 87, // get_service
                'role_id' => 3,
            ],
            [
                'permission_id' => 92, // get_user_me
                'role_id' => 3,
            ],
            [
                'permission_id' => 96, // update_user
                'role_id' => 3,
            ]
        ];

        $this->loadTable($dataUser);
    }
}
