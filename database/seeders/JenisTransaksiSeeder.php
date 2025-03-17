<?php

namespace Database\Seeders;

use App\Models\Jenis_Transaksi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JenisTransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis_transaksi = [
            ['jenis_transaksi' => 'Terikat'],
            ['jenis_transaksi' => 'Tidak Terikat'],
        ];

        foreach ($jenis_transaksi as $data) {
            Jenis_Transaksi::create($data);
        }
    }
}
