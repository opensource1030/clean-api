<?php

/**
 * PermissionTableSeeder - Insert info into database.
 *
 * @author   Marcio de Rezende
 */
class PermissionsTableSeeder extends BaseTableSeeder
{
    protected $table = 'permissions';

    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->deleteTable();

        /*
         *  ROLES
         *  scope:get_roles, scope:get_role, scope:create_role, scope:update_role, scope:delete_role
         *
         */
        $dataRole = [
             [
                'name' => 'get_roles',
                'display_name' => 'View all roles',
                'description' => 'View all roles',
                
            ],
            [
                'name' => 'get_role',
                'display_name' => 'View a role',
                'description' => 'View a role',
            ],
            [
                'name' => 'create_role',
                'display_name' => 'Create a role',
                'description' => 'Create a role',
                
            ],
            [
                'name' => 'update_role',
                'display_name' => 'Update a role',
                'description' => 'Update a role',
                
            ],
            [
                'name' => 'delete_role',
                'display_name' => 'Erase a role',
                'description' => 'Erase a role',
                
            ],
        ];

        /*
         *  PERMISSIONS
         *  scope:get_permissions, scope:get_permission, scope:create_permission, scope:update_permission,           *    scope:delete_permission
         *
         */
        $dataPermission = [
             [
                'name' => 'get_permissions',
                'display_name' => 'View all permissions',
                'description' => 'View all permissions',
                
            ],
            [
                'name' => 'get_permission',
                'display_name' => 'View a permission',
                'description' => 'View a permission',
            ],
            [
                'name' => 'create_permission',
                'display_name' => 'Create a permission',
                'description' => 'Create a permission',
                
            ],
            [
                'name' => 'update_permission',
                'display_name' => 'Update a permission',
                'description' => 'Update a permission',
                
            ],
            [
                'name' => 'delete_permission',
                'display_name' => 'Erase a permission',
                'description' => 'Erase a permission',
                
            ],
        ];

        /*
         *  SCOPES
         *  scope:get_scopes, scope:get_scope, scope:create_scope, scope:update_scope, scope:delete_scope
         *
         */
        $dataScope = [
             [
                'name' => 'get_scopes',
                'display_name' => 'View all scopes',
                'description' => 'View all scopes',
                
            ],
            [
                'name' => 'get_scope',
                'display_name' => 'View a scope',
                'description' => 'View a scope',
            ],
            [
                'name' => 'create_scope',
                'display_name' => 'Create a scope',
                'description' => 'Create a scope',
                
            ],
            [
                'name' => 'update_scope',
                'display_name' => 'Update a scope',
                'description' => 'Update a scope',
                
            ],
            [
                'name' => 'delete_scope',
                'display_name' => 'Erase a scope',
                'description' => 'Erase a scope',
                
            ],
        ];

        /*
         *  COMPANIES
         *  scope:get_companies, scope:get_company, scope:create_company, scope:update_company, scope:delete_company
         *
         */
        $dataCompany = [
            [
                'name' => 'get_companies',
                'display_name' => 'View all companies',
                'description' => 'View all companies',
            ],
            [
                'name' => 'get_company',
                'display_name' => 'View a company',
                'description' => 'View a company',
            ],
            [
                'name' => 'create_company',
                'display_name' => 'Create a company',
                'description' => 'Create a company',
                
            ],
            [
                'name' => 'update_company',
                'display_name' => 'Update a company',
                'description' => 'Update a company',
                
            ],
            [
                'name' => 'delete_company',
                'display_name' => 'Erase a company',
                'description' => 'Erase a company',
            ],
        ];

        /*
         *  USERS
         *  scope:get_users, scope:get_users_packages, scope:get_user_me, scope:get_user, scope:create_user,
         *  scope:update_user, scope:delete_user
         *
         */
        $dataUser = [
            [
                'name' => 'get_users',
                'display_name' => 'View all users',
                'description' => 'View all users',
            ],
            [
                'name' => 'get_users_packages',
                'display_name' => 'View the number of users allowed',
                'description' => 'View the number of users allowed',
            ],
            [
                'name' => 'get_user_me',
                'display_name' => 'View logged user information',
                'description' => 'View logged user information',
            ],
            [
                'name' => 'get_user',
                'display_name' => 'View a user',
                'description' => 'View a user',
            ],
            [
                'name' => 'create_user',
                'display_name' => 'Create a user',
                'description' => 'Create a user',
            ],
            [
                'name' => 'update_user',
                'display_name' => 'Update a users',
                'description' => 'Update a users',
            ],
            [
                'name' => 'delete_user',
                'display_name' => 'Delete a users',
                'description' => 'Delete a users',
            ],
        ];

        /*
         *  ASSETS
         *  scope:get_assets, scope:get_asset
         *
         */
        $dataAsset = [
             [
                'name' => 'get_assets',
                'display_name' => 'View all assets',
                'description' => 'View all assets',
                
            ],
            [
                'name' => 'get_asset',
                'display_name' => 'View an asset',
                'description' => 'View an asset',
            ],
        ];

        /*
         *  DEVICES
         *  scope:get_devices, scope:get_device, scope:create_device, scope:update_device, scope:delete_device
         *
         */
        $dataDevice = [
             [
                'name' => 'get_devices',
                'display_name' => 'View all devices',
                'description' => 'View all devices',
                
            ],
            [
                'name' => 'get_device',
                'display_name' => 'View a device',
                'description' => 'View a device',
            ],
            [
                'name' => 'create_device',
                'display_name' => 'Create a device',
                'description' => 'Create a device',
                
            ],
            [
                'name' => 'update_device',
                'display_name' => 'Update a device',
                'description' => 'Update a device',
                
            ],
            [
                'name' => 'delete_device',
                'display_name' => 'Erase a device',
                'description' => 'Erase a device',
                
            ],
        ];

        /*
         *  CONTENTS
         *  scope:get_contents, scope:get_content, scope:create_content, scope:update_content, scope:delete_content
         *
         */
        $dataContent = [
             [
                'name' => 'get_contents',
                'display_name' => 'View all contents',
                'description' => 'View all contents',
                
            ],
            [
                'name' => 'get_content',
                'display_name' => 'View a content',
                'description' => 'View a content',
            ],
            [
                'name' => 'create_content',
                'display_name' => 'Create a content',
                'description' => 'Create a content',
                
            ],
            [
                'name' => 'update_content',
                'display_name' => 'Update a content',
                'description' => 'Update a content',
                
            ],
            [
                'name' => 'delete_content',
                'display_name' => 'Erase a content',
                'description' => 'Erase a content',
                
            ],
        ];

        /*
         *  APPS
         *  scope:get_apps, scope:get_app, scope:create_app, scope:update_app, scope:delete_app
         *
         */
        $dataApp = [
             [
                'name' => 'get_apps',
                'display_name' => 'View all apps',
                'description' => 'View all apps',
                
            ],
            [
                'name' => 'get_app',
                'display_name' => 'View a app',
                'description' => 'View a app',
            ],
            [
                'name' => 'create_app',
                'display_name' => 'Create a app',
                'description' => 'Create a app',
                
            ],
            [
                'name' => 'update_app',
                'display_name' => 'Update a app',
                'description' => 'Update a app',
                
            ],
            [
                'name' => 'delete_app',
                'display_name' => 'Erase a app',
                'description' => 'Erase a app',
                
            ],
        ];

        /*
         *  ORDERS
         *  scope:get_orders, scope:get_order, scope:create_order, scope:update_order, scope:delete_order
         *
         */
        $dataOrder = [
             [
                'name' => 'get_orders',
                'display_name' => 'View all orders',
                'description' => 'View all orders',
                
            ],
            [
                'name' => 'get_order',
                'display_name' => 'View a order',
                'description' => 'View a order',
            ],
            [
                'name' => 'create_order',
                'display_name' => 'Create a order',
                'description' => 'Create a order',
                
            ],
            [
                'name' => 'update_order',
                'display_name' => 'Update a order',
                'description' => 'Update a order',
                
            ],
            [
                'name' => 'delete_order',
                'display_name' => 'Erase a order',
                'description' => 'Erase a order',
                
            ],
        ];

        /*
         *  PACKAGES
         *  scope:get_packages, scope:get_packages_foruser, scope:get_package, scope:create_package,                 *    scope:update_package, scope:delete_package
         *
         */
        $dataPackage = [
             [
                'name' => 'get_packages',
                'display_name' => 'View all packages',
                'description' => 'View all packages',
                
            ],
            [
                'name' => 'get_package',
                'display_name' => 'View a package',
                'description' => 'View a package',
            ],
            [
                'name' => 'get_packages_foruser',
                'display_name' => 'View all packages of the user',
                'description' => 'View all packages of the user',
            ],
            [
                'name' => 'create_package',
                'display_name' => 'Create a package',
                'description' => 'Create a package',
                
            ],
            [
                'name' => 'update_package',
                'display_name' => 'Update a package',
                'description' => 'Update a package',
                
            ],
            [
                'name' => 'delete_package',
                'display_name' => 'Erase a package',
                'description' => 'Erase a package',
                
            ],
        ];

        /*
         *  SERVICES
         *  scope:get_services, scope:get_service, scope:create_service, scope:update_service, scope:delete_service
         *
         */
        $dataService = [
             [
                'name' => 'get_services',
                'display_name' => 'View all services',
                'description' => 'View all services',
                
            ],
            [
                'name' => 'get_service',
                'display_name' => 'View a service',
                'description' => 'View a service',
            ],
            [
                'name' => 'create_service',
                'display_name' => 'Create a service',
                'description' => 'Create a service',
                
            ],
            [
                'name' => 'update_service',
                'display_name' => 'Update a service',
                'description' => 'Update a service',
                
            ],
            [
                'name' => 'delete_service',
                'display_name' => 'Erase a service',
                'description' => 'Erase a service',
                
            ],
        ];

        /*
         *  MODIFICATIONS
         *  scope:get_modifications, scope:get_modification, scope:create_modification, scope:update_modification,
         *    scope:delete_modification
         *
         */
        $dataModification = [
             [
                'name' => 'get_modifications',
                'display_name' => 'View all modifications',
                'description' => 'View all modifications',
                
            ],
            [
                'name' => 'get_modification',
                'display_name' => 'View a modification',
                'description' => 'View a modification',
            ],
            [
                'name' => 'create_modification',
                'display_name' => 'Create a modification',
                'description' => 'Create a modification',
                
            ],
            [
                'name' => 'update_modification',
                'display_name' => 'Update a modification',
                'description' => 'Update a modification',
                
            ],
            [
                'name' => 'delete_modification',
                'display_name' => 'Erase a modification',
                'description' => 'Erase a modification',
                
            ],
        ];

        /*
         *  CARRIERS
         *  scope:get_carriers, scope:get_carrier, scope:create_carrier, scope:update_carrier, scope:delete_carrier
         *
         */
        $dataCarrier = [
            [
                'name' => 'get_carriers',
                'display_name' => 'View all carriers',
                'description' => 'View all carriers',
                
            ],
            [
                'name' => 'get_carrier',
                'display_name' => 'View a carrier',
                'description' => 'View a carrier',
            ],
            [
                'name' => 'create_carrier',
                'display_name' => 'Create a carrier',
                'description' => 'Create a carrier',
                
            ],
            [
                'name' => 'update_carrier',
                'display_name' => 'Update a carrier',
                'description' => 'Update a carrier',
                
            ],
            [
                'name' => 'delete_carrier',
                'display_name' => 'Erase a carrier',
                'description' => 'Erase a carrier',
                
            ],
        ];

        /*
         *  DEVICEVARIATIONS
         *  scope:get_devicevariations, scope:get_devicevariation, scope:create_devicevariation,
         *    scope:update_devicevariation, scope:delete_devicevariation
         *
         */
        $dataDeviceVariation = [
             [
                'name' => 'get_prices',
                'display_name' => 'View all prices',
                'description' => 'View all prices',
                
            ],
            [
                'name' => 'get_price',
                'display_name' => 'View a price',
                'description' => 'View a price',
            ],
            [
                'name' => 'create_price',
                'display_name' => 'Create a price',
                'description' => 'Create a price',
                
            ],
            [
                'name' => 'update_price',
                'display_name' => 'Update a price',
                'description' => 'Update a price',
                
            ],
            [
                'name' => 'delete_price',
                'display_name' => 'Erase a price',
                'description' => 'Erase a price',
                
            ],
        ];

        /*
         *  IMAGES
         *  scope:get_images, scope:get_image, scope:get_image_info, scope:create_image, scope:delete_image
         *
         */
        $dataImage = [
             [
                'name' => 'get_images',
                'display_name' => 'View all images',
                'description' => 'View all images',
                
            ],
            [
                'name' => 'get_image',
                'display_name' => 'View a image',
                'description' => 'View a image',
            ],
            [
                'name' => 'get_image_info',
                'display_name' => 'View a image info',
                'description' => 'View a image info',
            ],
            [
                'name' => 'create_image',
                'display_name' => 'Create a image',
                'description' => 'Create a image',
                
            ],
            [
                'name' => 'delete_image',
                'display_name' => 'Erase a image',
                'description' => 'Erase a image',
                
            ],
        ];

        /*
         *  ADDRESS
         *  scope:get_addresses, scope:get_address, scope:create_address, scope:update_address, scope:delete_address
         *
         */
        $dataAddress = [
            [
                'name' => 'get_addresses',
                'display_name' => 'View all addresses',
                'description' => 'View all addresses',
                
            ],
            [
                'name' => 'get_address',
                'display_name' => 'View a address',
                'description' => 'View a address',
            ],
            [
                'name' => 'create_address',
                'display_name' => 'Create a address',
                'description' => 'Create a address',
                
            ],
            [
                'name' => 'update_address',
                'display_name' => 'Update a address',
                'description' => 'Update a address',
                
            ],
            [
                'name' => 'delete_address',
                'display_name' => 'Erase a address',
                'description' => 'Erase a address',
                
            ],
        ];

        /*
         *  DEVICETYPES
         *  scope:get_devicetypes, scope:get_devicetype, scope:create_devicetype, scope:update_devicetype,
         *    scope:delete_devicetype
         *
         */
        $dataDeviceType = [
            [
                'name' => 'get_devicetypes',
                'display_name' => 'View all devicetypes',
                'description' => 'View all devicetypes',
                
            ],
            [
                'name' => 'get_devicetype',
                'display_name' => 'View a devicetype',
                'description' => 'View a devicetype',
            ],
            [
                'name' => 'create_devicetype',
                'display_name' => 'Create a devicetype',
                'description' => 'Create a devicetype',
                
            ],
            [
                'name' => 'update_devicetype',
                'display_name' => 'Update a devicetype',
                'description' => 'Update a devicetype',
                
            ],
            [
                'name' => 'delete_devicetype',
                'display_name' => 'Erase a devicetype',
                'description' => 'Erase a devicetype',
                
            ],
        ];

        /*
         *  CONDITIONS
         *  scope:get_conditions, scope:get_condition, scope:create_condition, scope:updatecondition,
         *    scope:delete_condition
         *
         */
        $dataConditions = [
             [
                'name' => 'get_conditions',
                'display_name' => 'View all conditions',
                'description' => 'View all conditions',
                
            ],
            [
                'name' => 'get_condition',
                'display_name' => 'View a condition',
                'description' => 'View a condition',
            ],
            [
                'name' => 'create_condition',
                'display_name' => 'Create a condition',
                'description' => 'Create a condition',
                
            ],
            [
                'name' => 'update_condition',
                'display_name' => 'Update a condition',
                'description' => 'Update a condition',
                
            ],
            [
                'name' => 'delete_condition',
                'display_name' => 'Erase a condition',
                'description' => 'Erase a condition',
                
            ],
        ];

        $this->loadTable($dataRole);
        $this->loadTable($dataPermission);
        $this->loadTable($dataScope);
        $this->loadTable($dataCompany);
        $this->loadTable($dataDevice);
        $this->loadTable($dataUser);
        $this->loadTable($dataAsset);
        $this->loadTable($dataContent);
        $this->loadTable($dataApp);
        $this->loadTable($dataOrder);
        $this->loadTable($dataPackage);
        $this->loadTable($dataService);
        $this->loadTable($dataModification);
        $this->loadTable($dataCarrier);
        $this->loadTable($dataDeviceVariation);
        $this->loadTable($dataImage);
        $this->loadTable($dataAddress);
        $this->loadTable($dataDeviceType);
        $this->loadTable($dataConditions);
    }
}
