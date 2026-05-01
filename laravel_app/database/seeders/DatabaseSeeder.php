<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Sadeep',
            'email' => 'sadeep@example.com',
            'password' => bcrypt('password'),
        ]);

        // Add sample holdings
        \App\Models\Holding::create([
            'user_id' => $user->id,
            'symbol' => 'COMB.N0000',
            'quantity' => 1500,
            'avg_price' => 95.50,
        ]);

        \App\Models\Holding::create([
            'user_id' => $user->id,
            'symbol' => 'JKH.N0000',
            'quantity' => 2000,
            'avg_price' => 180.25,
        ]);

        \App\Models\Holding::create([
            'user_id' => $user->id,
            'symbol' => 'HNB.N0000',
            'quantity' => 800,
            'avg_price' => 165.00,
        ]);
    }
}
