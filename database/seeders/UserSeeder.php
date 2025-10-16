<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1) Ensure the "Admin" role exists (updateOrCreate by slug)
        $adminRole = Role::updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'slug' => 'admin']
        );

        // 2) Create or update the "admin" user by looking up on username (unique)
        User::updateOrCreate(
            // --- Lookup criteria: match existing record by 'username'
            ['username' => 'admin'],

            // --- Fillable attributes for the admin user
            [
                'role_id'         => $adminRole->id,
                'name'            => '1Admin',
                'email'           => '1admin@gmail.com',
                'phone'           => '101749699156',
                'is_approved'     => true,
                'joining_date'    => date('Y-m-d'),
                'joining_month'   => date('F'),
                'joining_year'    => date('Y'),
                'email_verified_at' => now(),
                // Hash a default password (change "password123" to whatever you prefer)
                'password'        => Hash::make('password123'),
                // Generate or overwrite the remember_token
                'remember_token'  => Str::random(10),
                // If you have other required columns (e.g. 'referer_id'), include them too:
                // 'referer_id'   => rand(pow(10, 5-1), pow(10, 5)-1),
            ]
        );

        // -------------------------------------------------------------------
        // You can chain more users below, for example a "customer" user:
        // -------------------------------------------------------------------
        $customerRole = Role::firstOrCreate(
            ['slug' => 'customer'],
            ['name' => 'Customer', 'slug' => 'customer']
        );

        User::updateOrCreate(
            ['username' => 'customer'],  // lookup by unique username
            [
                'role_id'         => $customerRole->id,
                'name'            => 'sCustomer',
                'email'           => 'scustomer@gmail.com',
                'phone'           => '101303851066',
                'is_approved'     => true,
                'joining_date'    => date('Y-m-d'),
                'joining_month'   => date('F'),
                'joining_year'    => date('Y'),
                'email_verified_at' => now(),
                'password'        => Hash::make('12345678'),
                'remember_token'  => Str::random(10),
                // 'referer_id'   => rand(pow(10, 5-1), pow(10, 5)-1),
            ]
        );
    }
}
