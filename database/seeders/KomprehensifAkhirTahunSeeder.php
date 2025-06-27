<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Komprehensif_Akhir_Tahun;
class KomprehensifAkhirTahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [

            
            // --------------------------------------PENERIMAAN DAN SUMBANGAN
            // Penerimaan dan Sumbangan Pendidikan
            ['id_akun' => '56', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 11344989000, 'tahun' => 2023], //spp
            ['id_akun' => '57', 'saldo_akhir_dengan_pembatasan' => 828504000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],    //POm
            ['id_akun' => '58', 'saldo_akhir_dengan_pembatasan' => 905393100, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],   //sarana belajar
            ['id_akun' => '59', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 2463643822, 'tahun' => 2023],   //pembangunan
            ['id_akun' => '60', 'saldo_akhir_dengan_pembatasan' => 11050000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],     //pemantapan
            ['id_akun' => '61', 'saldo_akhir_dengan_pembatasan' => 436719052, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],    //perpisahan
            ['id_akun' => '62', 'saldo_akhir_dengan_pembatasan' => 2407328989, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],   //bos bop
            ['id_akun' => '63', 'saldo_akhir_dengan_pembatasan' => 200000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],      //us
            ['id_akun' => '65', 'saldo_akhir_dengan_pembatasan' => 58852639, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],            //anbk
            ['id_akun' => '64', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],     //ukk
            ['id_akun' => '66', 'saldo_akhir_dengan_pembatasan' => 32654474, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],     //prakerin
            ['id_akun' => '67', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 80940000, 'tahun' => 2023],
            ['id_akun' => '68', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 166730000, 'tahun' => 2023],
            ['id_akun' => '69', 'saldo_akhir_dengan_pembatasan' => 210644938, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '70', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1917227348, 'tahun' => 2023],
            ['id_akun' => '71', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 970340139, 'tahun' => 2023],
            ['id_akun' => '72', 'saldo_akhir_dengan_pembatasan' => 158882000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '73', 'saldo_akhir_dengan_pembatasan' => 799453632, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '74', 'saldo_akhir_dengan_pembatasan' => 948414000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '75', 'saldo_akhir_dengan_pembatasan' => 13720000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '76', 'saldo_akhir_dengan_pembatasan' => 27840000, 'saldo_akhir_tanpa_pembatasan' => 81000000, 'tahun' => 2023],


            // Penerimaan dan Sumbangan Non Pendidikan
            ['id_akun' => '77', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '78', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '79', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '80', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],
            ['id_akun' => '81', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 8400420, 'tahun' => 2023],
            ['id_akun' => '82', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],





            // --------------------------------------BEBAN
            //Beban Operasional
            ['id_akun' => '83', 'saldo_akhir_dengan_pembatasan' => 1222859800, 'saldo_akhir_tanpa_pembatasan' => 7485760785, 'tahun' => 2023], //beban gaji
            ['id_akun' => '84', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023],  //honorium
            ['id_akun' => '85', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 543519657, 'tahun' => 2023],  //thr
            ['id_akun' => '86', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //transport
            ['id_akun' => '87', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 156456600, 'tahun' => 2023], //BBM
            ['id_akun' => '88', 'saldo_akhir_dengan_pembatasan' => 194559635, 'saldo_akhir_tanpa_pembatasan' => 148135350, 'tahun' => 2023], //ATK
            ['id_akun' => '89', 'saldo_akhir_dengan_pembatasan' => 683718636, 'saldo_akhir_tanpa_pembatasan' => 402698460, 'tahun' => 2023], //Keg Siswa
            ['id_akun' => '90', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1681357158, 'tahun' => 2023], //peny bangunan
            ['id_akun' => '91', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 282200529, 'tahun' => 2023], //Peny inventaris kantor
            ['id_akun' => '92', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 26850005, 'tahun' => 2023], //Peny kendaraan
            ['id_akun' => '93', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 122612430, 'tahun' => 2023], // Peny Peralatan
            ['id_akun' => '94', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 136795622, 'tahun' => 2023], // Listrik
            ['id_akun' => '95', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 17496300, 'tahun' => 2023], // internet
            ['id_akun' => '96', 'saldo_akhir_dengan_pembatasan' => 106288852, 'saldo_akhir_tanpa_pembatasan' => 691090709, 'tahun' => 2023], //Konsumsi
            ['id_akun' => '97', 'saldo_akhir_dengan_pembatasan' => 49540000, 'saldo_akhir_tanpa_pembatasan' => 312350918, 'tahun' => 2023], //Kerumahtanggaan lainnya
            ['id_akun' => '98', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 2800000, 'tahun' => 2023], //bpjs
            ['id_akun' => '99', 'saldo_akhir_dengan_pembatasan' => 21844000, 'saldo_akhir_tanpa_pembatasan' => 514341941, 'tahun' => 2023], //perbaikan asset
            ['id_akun' => '100', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //pembangunan
            ['id_akun' => '101', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 97578206, 'tahun' => 2023], //dinas
            ['id_akun' => '102', 'saldo_akhir_dengan_pembatasan' => 8315400, 'saldo_akhir_tanpa_pembatasan' => 77619500, 'tahun' => 2023],  //dokumentasi
            ['id_akun' => '103', 'saldo_akhir_dengan_pembatasan' => 4700000, 'saldo_akhir_tanpa_pembatasan' => 188950900, 'tahun' => 2023], //pelatihan
            ['id_akun' => '104', 'saldo_akhir_dengan_pembatasan' => 87366000, 'saldo_akhir_tanpa_pembatasan' => 77102436, 'tahun' => 2023], //ekstrakulikuler
            ['id_akun' => '105', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 284400, 'tahun' => 2023], //expedisi
            ['id_akun' => '106', 'saldo_akhir_dengan_pembatasan' => 23302000, 'saldo_akhir_tanpa_pembatasan' => 1694064100, 'tahun' => 2023], //pengeluaran buku
            ['id_akun' => '107', 'saldo_akhir_dengan_pembatasan' => 68980000, 'saldo_akhir_tanpa_pembatasan' => 832639000, 'tahun' => 2023], //seragam siswa
            ['id_akun' => '108', 'saldo_akhir_dengan_pembatasan' => 751057500, 'saldo_akhir_tanpa_pembatasan' => 39351000, 'tahun' => 2023], //Catering
            ['id_akun' => '109', 'saldo_akhir_dengan_pembatasan' => 929835000, 'saldo_akhir_tanpa_pembatasan' => 9430000, 'tahun' => 2023], // transportasi siswa
            ['id_akun' => '110', 'saldo_akhir_dengan_pembatasan' => 1727493801, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //bos bop
            ['id_akun' => '111', 'saldo_akhir_dengan_pembatasan' => 822970100, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], // dana komite
            ['id_akun' => '112', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //op lainnya
            ['id_akun' => '113', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 119066667, 'tahun' => 2023], //beban sewa
            ['id_akun' => '114', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //beban asuransi

            
            //Beban Non Operasional
            ['id_akun' => '115', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 90631581, 'tahun' => 2023], // beban adm bank
            ['id_akun' => '116', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 626720779, 'tahun' => 2023], // adm pinjaman
            ['id_akun' => '117', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1070000, 'tahun' => 2023], // koran
            ['id_akun' => '118', 'saldo_akhir_dengan_pembatasan' => 3885000, 'saldo_akhir_tanpa_pembatasan' => 27569000, 'tahun' => 2023], // perawatan
            ['id_akun' => '119', 'saldo_akhir_dengan_pembatasan' => 24355000, 'saldo_akhir_tanpa_pembatasan' => 359933174, 'tahun' => 2023], //taawun
            ['id_akun' => '120', 'saldo_akhir_dengan_pembatasan' => 108586100, 'saldo_akhir_tanpa_pembatasan' => 18473030, 'tahun' => 2023], //foto copy
            ['id_akun' => '121', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 18005128, 'tahun' => 2023], //pajak penghasilan
            ['id_akun' => '122', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 4674800, 'tahun' => 2023], //pajak kendaraan
            ['id_akun' => '123', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 41265300, 'tahun' => 2023], //pbb
            ['id_akun' => '124', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2023], //non op lainnya

            






            
            // --------------------------------------PENERIMAAN DAN SUMBANGAN
            // Penerimaan dan Sumbangan Pendidikan
            ['id_akun' => '56', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 12332712903, 'tahun' => 2024], //spp
            ['id_akun' => '57', 'saldo_akhir_dengan_pembatasan' => 947507000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],    //POm
            ['id_akun' => '58', 'saldo_akhir_dengan_pembatasan' => 1356664440, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],   //sarana belajar
            ['id_akun' => '59', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 2869364134, 'tahun' => 2024],   //pembangunan
            ['id_akun' => '60', 'saldo_akhir_dengan_pembatasan' => 13793000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],     //pemantapan
            ['id_akun' => '61', 'saldo_akhir_dengan_pembatasan' => 269354016, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],    //perpisahan
            ['id_akun' => '62', 'saldo_akhir_dengan_pembatasan' => 2949514753, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],   //bos bop
            ['id_akun' => '63', 'saldo_akhir_dengan_pembatasan' => 1820000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],      //us
            ['id_akun' => '65', 'saldo_akhir_dengan_pembatasan' => 41892656, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],            //anbk
            ['id_akun' => '64', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],     //ukk
            ['id_akun' => '66', 'saldo_akhir_dengan_pembatasan' => 31054992, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],     //prakerin
            ['id_akun' => '67', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 221561000, 'tahun' => 2024],
            ['id_akun' => '68', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 246715000, 'tahun' => 2024],
            ['id_akun' => '69', 'saldo_akhir_dengan_pembatasan' => 131686754, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '70', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 2113674340, 'tahun' => 2024],
            ['id_akun' => '71', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1200421656, 'tahun' => 2024],
            ['id_akun' => '72', 'saldo_akhir_dengan_pembatasan' => 212566100, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '73', 'saldo_akhir_dengan_pembatasan' => 661774000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '74', 'saldo_akhir_dengan_pembatasan' => 1069768500, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '75', 'saldo_akhir_dengan_pembatasan' => 70728000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '76', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 516426739, 'tahun' => 2024],


            // Penerimaan dan Sumbangan Non Pendidikan
            ['id_akun' => '77', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '78', 'saldo_akhir_dengan_pembatasan' => 96535000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '79', 'saldo_akhir_dengan_pembatasan' => 4100000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '80', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '81', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1215732, 'tahun' => 2024],
            ['id_akun' => '82', 'saldo_akhir_dengan_pembatasan' => 50000000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],





            // --------------------------------------BEBAN
            //Beban Operasional
            ['id_akun' => '83', 'saldo_akhir_dengan_pembatasan' => 999568000, 'saldo_akhir_tanpa_pembatasan' => 8558288090, 'tahun' => 2024],
            ['id_akun' => '84', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '85', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 608812823, 'tahun' => 2024],
            ['id_akun' => '86', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '87', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 177380200, 'tahun' => 2024],
            ['id_akun' => '88', 'saldo_akhir_dengan_pembatasan' => 146737950, 'saldo_akhir_tanpa_pembatasan' => 159352800, 'tahun' => 2024],
            ['id_akun' => '89', 'saldo_akhir_dengan_pembatasan' => 1601832127, 'saldo_akhir_tanpa_pembatasan' => 134567794, 'tahun' => 2024],
            ['id_akun' => '90', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1778729503, 'tahun' => 2024],
            ['id_akun' => '91', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 334881695, 'tahun' => 2024],
            ['id_akun' => '92', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 26850000, 'tahun' => 2024],
            ['id_akun' => '93', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 133384377, 'tahun' => 2024],
            ['id_akun' => '94', 'saldo_akhir_dengan_pembatasan' => 8761819, 'saldo_akhir_tanpa_pembatasan' => 114785699, 'tahun' => 2024],
            ['id_akun' => '95', 'saldo_akhir_dengan_pembatasan' => 2968360, 'saldo_akhir_tanpa_pembatasan' => 23604135, 'tahun' => 2024],
            ['id_akun' => '96', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1305896179, 'tahun' => 2024],
            ['id_akun' => '97', 'saldo_akhir_dengan_pembatasan' => 89426600, 'saldo_akhir_tanpa_pembatasan' => 361872137, 'tahun' => 2024],
            ['id_akun' => '98', 'saldo_akhir_dengan_pembatasan' => 5241000, 'saldo_akhir_tanpa_pembatasan' => 26442370, 'tahun' => 2024],
            ['id_akun' => '99', 'saldo_akhir_dengan_pembatasan' => 48330000, 'saldo_akhir_tanpa_pembatasan' => 431792187, 'tahun' => 2024],
            ['id_akun' => '100', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '101', 'saldo_akhir_dengan_pembatasan' => 1650000, 'saldo_akhir_tanpa_pembatasan' => 72409300, 'tahun' => 2024],
            ['id_akun' => '102', 'saldo_akhir_dengan_pembatasan' => 1630000, 'saldo_akhir_tanpa_pembatasan' => 13211600, 'tahun' => 2024],
            ['id_akun' => '103', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 212779119, 'tahun' => 2024],
            ['id_akun' => '104', 'saldo_akhir_dengan_pembatasan' => 60163240, 'saldo_akhir_tanpa_pembatasan' => 32024500, 'tahun' => 2024],
            ['id_akun' => '105', 'saldo_akhir_dengan_pembatasan' => 628150, 'saldo_akhir_tanpa_pembatasan' => 65500, 'tahun' => 2024],
            ['id_akun' => '106', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1882053212, 'tahun' => 2024],
            ['id_akun' => '107', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1137483500, 'tahun' => 2024],
            ['id_akun' => '108', 'saldo_akhir_dengan_pembatasan' => 633866000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '109', 'saldo_akhir_dengan_pembatasan' => 1077609000, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '110', 'saldo_akhir_dengan_pembatasan' => 2382280322, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '111', 'saldo_akhir_dengan_pembatasan' => 685587133, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '112', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],
            ['id_akun' => '113', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 116666664, 'tahun' => 2024],
            ['id_akun' => '114', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],

            //Beban Non Operasional
            ['id_akun' => '115', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 91160106, 'tahun' => 2024],
            ['id_akun' => '116', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 586846280, 'tahun' => 2024],
            ['id_akun' => '117', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 1152000, 'tahun' => 2024],
            ['id_akun' => '118', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 18016168, 'tahun' => 2024],
            ['id_akun' => '119', 'saldo_akhir_dengan_pembatasan' => 27821110, 'saldo_akhir_tanpa_pembatasan' => 233320045, 'tahun' => 2024],
            ['id_akun' => '120', 'saldo_akhir_dengan_pembatasan' => 134658400, 'saldo_akhir_tanpa_pembatasan' => 2303000, 'tahun' => 2024],
            ['id_akun' => '121', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 12807000, 'tahun' => 2024],
            ['id_akun' => '122', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 5057300, 'tahun' => 2024],
            ['id_akun' => '123', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 15161276, 'tahun' => 2024],
            ['id_akun' => '124', 'saldo_akhir_dengan_pembatasan' => 0, 'saldo_akhir_tanpa_pembatasan' => 0, 'tahun' => 2024],

            








        ];

        foreach ($akun as $data) {
            Komprehensif_Akhir_Tahun::create($data); 
        }
    }
}
