<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\AkunSeeder;
use Database\Seeders\KategoriAkunSeeder;
use Database\Seeders\JenisTransaksiSeeder;
use Database\Seeders\SubKategoriAkunSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(KategoriAkunSeeder::class);
        $this->call(SubKategoriAkunSeeder::class);
        $this->call(AkunSeeder::class);
        $this->call(JenisTransaksiSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(DivisiSeeder::class);

    }
}
