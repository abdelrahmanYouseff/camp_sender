<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'status' => 'active',
                'company_id' => null,
            ]
        );

        $company = Company::updateOrCreate(
            ['email' => 'company_demo@gmail.com'],
            [
                'name' => 'Demo Company',
                'email' => 'company_demo@gmail.com',
                'phone' => null,
                'subscription_status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'company_demo@gmail.com'],
            [
                'name' => 'Demo Company User',
                'email' => 'company_demo@gmail.com',
                'password' => bcrypt('password123'),
                'role' => 'company_admin',
                'status' => 'active',
                'company_id' => $company->id,
            ]
        );

        $this->call(DemoCompanySeeder::class);

        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
