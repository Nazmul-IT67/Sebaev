<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'gender' => 'male',
                'date_of_birth' => '1980-01-01',
                'country_id' => 4,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                // 'role' => 'admin',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'individual@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1990-01-01',
                'country_id' => 3,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                // 'role' => 'individual',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'organization@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1990-01-01',
                'country_id' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                // 'role' => 'organization',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'name' => 'Sadika',
                'email' => 'sadikaafrin@gmail.com',
                'gender' => 'female',
                'date_of_birth' => '1992-05-15',
                'country_id' => 2,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                // 'role' => 'organization',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
            [
                'name' => 'RKB',
                'email' => 'reshikash300@gmail.com',
                'gender' => 'male',
                'date_of_birth' => '1990-01-01',
                'country_id' => 10,
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                // 'role' => 'organization',
                'remember_token' => Str::random(10),
                'created_at' => now(),
            ],
        ]);

        $super_admin = User::where('email', 'admin@admin.com')->first();

        if ($super_admin) {
            $super_admin->assignRole('super_admin');
        }
    }
}
