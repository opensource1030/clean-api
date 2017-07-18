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

        while ($i < 123) {
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
        while ($j < 123) {
            // NOT assign the permissions listed below.
            if(    $j != 22 // create_company
                && $j != 24 // delete_company
                && $j != 33 // delete_condition
                && $j != 54 // get_images
                && $j != 58 // delete_image
                && $j != 104 // create_permission
                && $j != 105 // update_permission
                && $j != 106 // delete_permission
                && $j != 107 // get_scopes
                && $j != 108 // get_scope
                && $j != 109 // create_scope
                && $j != 110 // update_scope
                && $j != 111 // delete_scope
                && $j != 122 // manage_companies
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
                'permission_id' => 49, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 50, // get_devicevariations
                'role_id' => 3,
            ],
            [
                'permission_id' => 55, // get_image
                'role_id' => 3,
            ],
            [
                'permission_id' => 56, // get_image_info
                'role_id' => 3,
            ],
            [
                'permission_id' => 64, // get_orders
                'role_id' => 3,
            ],
            [
                'permission_id' => 65, // get_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 66, // create_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 67, // update_order
                'role_id' => 3,
            ],
            [
                'permission_id' => 70, // get_packages_foruser
                'role_id' => 3,
            ],
            [
                'permission_id' => 71, // get_package
                'role_id' => 3,
            ],
            [
                'permission_id' => 85, // get_services
                'role_id' => 3,
            ],
            [
                'permission_id' => 86, // get_service
                'role_id' => 3,
            ],
            [
                'permission_id' => 92, // get_user_me
                'role_id' => 3,
            ],
            [
                'permission_id' => 95, // update_user
                'role_id' => 3,
            ]
        ];

        $this->loadTable($dataUser);
    }
}
