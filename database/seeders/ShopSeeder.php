<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user di database, jika tidak ada, kita ambil user ID 1
        // Atau kamu bisa menyesuaikan dengan user yang sudah kamu buat
        $userIds = User::pluck('id')->toArray();

        // Jika tabel user masih kosong, gunakan ID 1 sebagai fallback (pastikan sudah ada user)
        $fallbackUserId = !empty($userIds) ? $userIds[0] : 1;

        $shops = [
            [
                'name' => 'Tech Mega Store',
                'description' => 'Pusat gadget dan laptop original dengan garansi resmi.',
                'image' => 'shop-tech.jpg',
                'city' => 'Jakarta Barat',
                'is_online' => true,
                'user_id' => $userIds[0] ?? $fallbackUserId,
            ],
            [
                'name' => 'Fashion Forward ID',
                'description' => 'Menyediakan pakaian trendi dan berkualitas tinggi.',
                'image' => 'shop-fashion.jpg',
                'city' => 'Bandung',
                'is_online' => true,
                'user_id' => $userIds[1] ?? $fallbackUserId,
            ],
            [
                'name' => 'Gayo Coffee Official',
                'description' => 'Distributor resmi kopi asli tanah Gayo langsung dari petani.',
                'image' => 'shop-coffee.jpg',
                'city' => 'Aceh Tengah',
                'is_online' => false,
                'user_id' => $userIds[2] ?? $fallbackUserId,
            ],
            [
                'name' => 'Home & Decor Specialist',
                'description' => 'Lengkapi rumah impianmu dengan furnitur minimalis kami.',
                'image' => 'shop-home.jpg',
                'city' => 'Tangerang',
                'is_online' => true,
                'user_id' => $userIds[3] ?? $fallbackUserId,
            ],
            [
                'name' => 'Glow Up Beauty Bar',
                'description' => 'Skincare dan kosmetik terpercaya untuk kecantikan kulitmu.',
                'image' => 'shop-beauty.jpg',
                'city' => 'Surabaya',
                'is_online' => true,
                'user_id' => $userIds[4] ?? $fallbackUserId,
            ],
        ];

        foreach ($shops as $shop) {
            Shop::create([
                'name' => $shop['name'],
                'slug' => Str::slug($shop['name']),
                'description' => $shop['description'],
                'image' => $shop['image'],
                'city' => $shop['city'],
                'is_online' => $shop['is_online'],
                'user_id' => $shop['user_id'],
            ]);
        }
    }
}