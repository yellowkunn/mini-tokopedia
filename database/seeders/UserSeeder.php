<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin - Untuk kelola seluruh sistem
        User::create([
            'name' => 'Ahsanul Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'gender' => 'male',
            'birth_date' => '2000-01-01',
            'email_verified_at' => now(),
        ]);

        // 2. Seller - Untuk kelola toko dan produk (seperti di ShopSeeder sebelumnya)
        User::create([
            'name' => 'Muhammad Seller',
            'email' => 'seller@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'seller',
            'phone' => '081298765432',
            'gender' => 'male',
            'birth_date' => '1995-05-20',
            'email_verified_at' => now(),
        ]);

        // 3. Customer - Untuk ngetes proses checkout/beli
        User::create([
            'name' => 'Budi Customer',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '085712345678',
            'gender' => 'male',
            'birth_date' => '1998-10-15',
            'email_verified_at' => now(),
        ]);

        // Opsional: Tambah beberapa user random untuk meramaikan database
        User::factory(5)->create();
    }
}