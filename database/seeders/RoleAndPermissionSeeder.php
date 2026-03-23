<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permission
        $permissions = [
            // permissions admin
            'manage users',
            'manage categories_products',
            'manage products',
            'manage categories_articles',
            'manage articles',
            'manage sponsors',
            'manage settings',
            'manage live_sessions',
            'manage subscriptions',
            'manage chat',

            // permissions mentors
            'reply chat',

            // permissions petani premium
            'join live_sessions',

            // permissions petani basic
            'chat with_mentors',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // role admin
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            'manage users',
            'manage categories_products',
            'manage products',
            'manage categories_articles',
            'manage articles',
            'manage sponsors',
            'manage settings',
            'manage live_sessions',
            'manage subscriptions',
            'manage chat',
            'reply chat',
            'chat with_mentors',
            'join live_sessions',
        ]);

        // role mentor
        $mentorRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'mentor', 'guard_name' => 'web']);

        $mentorRole->syncPermissions([
            'manage articles',
            'manage chat',
            'reply chat',
            'join live_sessions',
        ]);

        // role petani
        $petaniRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'petani', 'guard_name' => 'web']);
        
        $petaniRole->syncPermissions([
            'chat with_mentors',
            'join live_sessions',
        ]);
    }
}
