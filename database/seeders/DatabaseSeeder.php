<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            ChurchOfficeAccountsSeeder::class,
        ]);
        
        $watotoDept = Department::where('name_sw', 'Idara ya Watoto')->first();

        // 1. Kiongozi Demo Account
        $kiongozi = User::firstOrCreate(
            ['email' => 'kiongozi@kanisa.com'],
            [
                'name' => 'Kiongozi Mwenyekiti',
                'phone' => '0712345678',
                'role' => 'kiongozi',
                'cheo' => 'Mwenyekiti',
                'password' => Hash::make('password'),
            ]
        );
        if ($watotoDept) {
            $kiongozi->departments()->syncWithoutDetaching([
                $watotoDept->id => ['role' => 'kiongozi', 'cheo' => 'Mwenyekiti']
            ]);
        }

        // 2. Mshirika Demo Account
        $mshirika = User::firstOrCreate(
            ['email' => 'mshirika@kanisa.com'],
            [
                'name' => 'Mshirika John',
                'phone' => '0787654321',
                'role' => 'mshirika',
                'password' => Hash::make('password'),
            ]
        );
        if ($watotoDept) {
            $mshirika->departments()->syncWithoutDetaching([
                $watotoDept->id => ['role' => 'mshirika']
            ]);
        }
    }
}
