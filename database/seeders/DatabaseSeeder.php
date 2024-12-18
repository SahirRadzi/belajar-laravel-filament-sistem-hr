<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'MrSuperAdmin',
            'email' => 'sahir.radzi@gmail.com',
            'password' => Hash::make('12345678'),
            'nric' => 931104086159,
            'phone' => 601234567891,
        ]);
    }
}
