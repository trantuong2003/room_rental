<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();


        $user = new User();
        $user->name = 'Admin';
        $user->email = 'admin@gmail.com';
        $user->role = 'admin';
        $user->password = bcrypt('123456');
        $user->save();


        // Tạo tài khoản Customer
        $customer = new User();
        $customer->name = 'Customer';
        $customer->email = 'customer@gmail.com';
        $customer->role = 'customer';
        $customer->password = bcrypt('123456789');
        $customer->email_verified_at = now(); // Không cần xác minh email
        $customer->save();

        // Tạo tài khoản Landlord
        $landlord = new User();
        $landlord->name = 'Landlord';
        $landlord->email = 'landlord@gmail.com';
        $landlord->role = 'landlord';
        $landlord->password = bcrypt('123456789');
        $landlord->email_verified_at = now(); // Không cần xác minh email
        $landlord->save();
    }
}
