<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserStorySeeder extends BaseSeeder
{
    /**
     * Credentials
     */
    const ADMIN_CREDENTIALS = [
        'email' => 'admin@admin.com',
    ];

    public function runFake()
    {
        // Grab all roles for reference
        $roles = Role::all();

        // Create an admin user
        \App\Models\User::factory()->create([
            'name'         => 'Administrator',
            'descriptions'         => 'Administrator',
            'email'        => static::ADMIN_CREDENTIALS['email'],
            'role_id' => $roles->where('name', 'admin')->first()->id,
            'password'         => Hash::make('password'),
        ]);

        // Create regular user
        \App\Models\User::factory()->create([
            'name'         => 'Mentor User',
            'email'        => 'mentor@mentor.com',
            'descriptions'         => 'Mentor',
            'institute'         => 'Tokopaedi',
            'profession'         => 'Sofware Engineer',
            'role_id' => $roles->where('name', 'mentor')->first()->id,
            'password'         => Hash::make('password'),
        ]);

        // Create regular user
        \App\Models\User::factory()->create([
            'name'         => 'Enrico Feraldo',
            'email'        => 'icoferaldo@gmail.com',
            'role_id' => $roles->where('name', 'end-user')->first()->id,
            'password'         => Hash::make('password'),
            'institute' => 'LP3I Jakarta',
            'profession' => 'Mahasiswa'
        ]);

        // Get some random roles to assign to users
        $fakeRolesToAssignCount = 3;
        $fakeRolesToAssign = RoleTableSeeder::getRandomRoles($fakeRolesToAssignCount);

        // Assign fake roles to users
        for ($i = 0; $i < 3; ++$i) {
            $user = \App\Models\User::factory()->create([
                'role_id' => $roles->random()->id,
                'institute' => 'Kebun Pisang',
                'profession' => 'Joki Skripsi'
            ]);

            for ($j = 0; $j < count($fakeRolesToAssign); ++$j) {
                $user->roles()->save($fakeRolesToAssign->shift());
            }
        }
    }
}
