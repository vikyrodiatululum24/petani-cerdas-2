<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Sistem',
                'email' => 'admin@petanicerdas.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Petani Demo',
                'email' => 'petani@petanicerdas.id',
                'password' => Hash::make('password'),
                'role' => 'petani',
            ],
            [
                'name' => 'Konsultan Demo',
                'email' => 'konsultan@petanicerdas.id',
                'password' => Hash::make('password'),
                'role' => 'mentor',
            ],
            [
                'name' => 'Mentor Demo',
                'email' => 'mentor@petanicerdas.id',
                'password' => Hash::make('password'),
                'role' => 'mentor',
            ],
        ];

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($users as $data) {
            $roleName = $data['role'];
            unset($data['role']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                $data,
            );

            $user->syncRoles([$roleName]);
        }
    }
}
