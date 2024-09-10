<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // List of permissions to be added
        $permissions = [
            'view_sidemenu',
            'create_sidemenu',
            'update_sidemenu',
            'delete_sidemenu',

            'view_brand',
            'create_brand',
            'update_brand',
            'delete_brand',

            'view_branch',
            'create_branch',
            'update_branch',
            'delete_branch',

            'view_user',
            'create_user',
            'update_user',
            'delete_user',

            'view_roles',
            'create_roles',
            'update_roles',
            'delete_roles',

            'view_permission',
            'create_permission',
            'update_permission',
            'delete_permission',

            'view_pro_type',
            'create_pro_type',
            'update_pro_type',
            'delete_pro_type',

            'view_pro_brand',
            'create_pro_brand',
            'update_pro_brand',
            'delete_pro_brand',

            'view_pro_category',
            'create_pro_category',
            'update_pro_category',
            'delete_pro_category',

            'view_pro_sub_category',
            'create_pro_sub_category',
            'update_pro_sub_category',
            'delete_pro_sub_category',

            'view_pro_info',
            'create_pro_info',
            'update_pro_info',
            'delete_pro_info',

            'view_dashboard',
            'create_dashboard',
            'update_dashboard',
            'delete_dashboard',
        ];

        // Iterate over the permissions and create them
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
