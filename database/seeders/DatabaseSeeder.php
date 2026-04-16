<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 5 kullanıcı oluştur (PostSeeder ve VideoSeeder bunları kullanacak)
        User::factory(5)->create();

        $this->call([
            PostSeeder::class,
            VideoSeeder::class,
        ]);
    }
}
