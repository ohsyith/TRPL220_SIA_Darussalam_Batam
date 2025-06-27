<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Saldo_Akhir_Tahun;
class SaldoAkhirTahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [

            // --------------------------------------AKTIVA
            // Kas
            ['id_akun' => '1', 'saldo_akhir' => 67939645, 'tahun' => 2023],
            ['id_akun' => '2', 'saldo_akhir' => 3410448, 'tahun' => 2023],
            ['id_akun' => '3', 'saldo_akhir' => 0, 'tahun' => 2023],

            // Bank
            ['id_akun' => '4', 'saldo_akhir' => 2960893247, 'tahun' => 2023],
            ['id_akun' => '5', 'saldo_akhir' => 422072764, 'tahun' => 2023],
            ['id_akun' => '6', 'saldo_akhir' => 127552702, 'tahun' => 2023],
            ['id_akun' => '7', 'saldo_akhir' => 306068362, 'tahun' => 2023],
            ['id_akun' => '8', 'saldo_akhir' => 102607264, 'tahun' => 2023],
            ['id_akun' => '9', 'saldo_akhir' => 387207033, 'tahun' => 2023],
            ['id_akun' => '10', 'saldo_akhir' => 119779998, 'tahun' => 2023],
            ['id_akun' => '11', 'saldo_akhir' => 249063430, 'tahun' => 2023],
            ['id_akun' => '12', 'saldo_akhir' => 6607768, 'tahun' => 2023],
            ['id_akun' => '13', 'saldo_akhir' => 0, 'tahun' => 2023],

            // Persediaan
            ['id_akun' => '14', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '15', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '16', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '17', 'saldo_akhir' => 0, 'tahun' => 2023],
            
            // Piutang
            ['id_akun' => '18', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '19', 'saldo_akhir' => 51109000, 'tahun' => 2023],
            ['id_akun' => '20', 'saldo_akhir' => 65900000, 'tahun' => 2023],
            ['id_akun' => '21', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '22', 'saldo_akhir' => 0, 'tahun' => 2023],
            
            // Aset Lancar Lainnya
            ['id_akun' => '23', 'saldo_akhir' => 123611112, 'tahun' => 2023],
            ['id_akun' => '24', 'saldo_akhir' => 1066210260, 'tahun' => 2023],
            ['id_akun' => '25', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '26', 'saldo_akhir' => 0, 'tahun' => 2023],
            
            // Aktiva Tetap
            ['id_akun' => '27', 'saldo_akhir' => 20949681000, 'tahun' => 2023],
            ['id_akun' => '28', 'saldo_akhir' => 36739030979, 'tahun' => 2023],
            ['id_akun' => '29', 'saldo_akhir' => 1104408400, 'tahun' => 2023],
            ['id_akun' => '30', 'saldo_akhir' => 1484750233, 'tahun' => 2023],
            ['id_akun' => '31', 'saldo_akhir' => 103793600, 'tahun' => 2023],
            ['id_akun' => '32', 'saldo_akhir' => 369455350, 'tahun' => 2023],
            ['id_akun' => '33', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '34', 'saldo_akhir' => -14125857422, 'tahun' => 2023],
            ['id_akun' => '35', 'saldo_akhir' => -865382093, 'tahun' => 2023],
            ['id_akun' => '36', 'saldo_akhir' => -483810995, 'tahun' => 2023],
            ['id_akun' => '37', 'saldo_akhir' => -217215907, 'tahun' => 2023],
            ['id_akun' => '38', 'saldo_akhir' =>  0, 'tahun' => 2023],








            // --------------------------------------KEWAJIBAN
            // Kewajiban Jangka Pendek
            ['id_akun' => '39', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '40', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '41', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '42', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '43', 'saldo_akhir' => 1066210260, 'tahun' => 2023],
            ['id_akun' => '44', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '45', 'saldo_akhir' => 2709109, 'tahun' => 2023],
            ['id_akun' => '46', 'saldo_akhir' => 5720600, 'tahun' => 2023],
            ['id_akun' => '47', 'saldo_akhir' => 2941999400, 'tahun' => 2023],
            ['id_akun' => '48', 'saldo_akhir' => 180850000, 'tahun' => 2023],
            ['id_akun' => '49', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '50', 'saldo_akhir' => 0, 'tahun' => 2023],

            // Kewajiban Jangka Panjang
            ['id_akun' => '51', 'saldo_akhir' => 4655419117, 'tahun' => 2023],
            ['id_akun' => '52', 'saldo_akhir' => 2000000000, 'tahun' => 2023],
            ['id_akun' => '53', 'saldo_akhir' => 0, 'tahun' => 2023],

            









            // --------------------------------------ASET NETO
            ['id_akun' => '54', 'saldo_akhir' => 2809987526, 'tahun' => 2023],
            ['id_akun' => '55', 'saldo_akhir' => 38386990166, 'tahun' => 2023],











            // --------------------------------------PENERIMAAN DAN SUMBANGAN
            // Penerimaan dan Sumbangan Pendidikan
            ['id_akun' => '56', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '57', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '58', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '59', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '60', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '61', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '62', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '63', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '64', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '65', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '66', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '67', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '68', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '69', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '70', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '71', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '72', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '73', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '74', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '75', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '76', 'saldo_akhir' => 0, 'tahun' => 2023],


            // Penerimaan dan Sumbangan Non Pendidikan
            ['id_akun' => '77', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '78', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '79', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '80', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '81', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '82', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '83', 'saldo_akhir' => 0, 'tahun' => 2023],












            // --------------------------------------BEBAN
            //Beban Operasional
            ['id_akun' => '84', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '85', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '86', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '87', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '88', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '89', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '90', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '91', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '92', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '93', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '94', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '95', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '96', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '97', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '98', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '99', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '100', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '101', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '102', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '103', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '104', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '105', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '106', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '107', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '108', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '109', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '110', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '111', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '112', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '113', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '114', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '115', 'saldo_akhir' => 0, 'tahun' => 2023],

            //Beban Non Operasional
            ['id_akun' => '116', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '117', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '118', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '119', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '120', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '121', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '122', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '123', 'saldo_akhir' => 0, 'tahun' => 2023],
            ['id_akun' => '124', 'saldo_akhir' => 0, 'tahun' => 2023],

            
















            
            // --------------------------------------AKTIVA
            // Kas
            ['id_akun' => '1', 'saldo_akhir' => 53676453, 'tahun' => 2024],
            ['id_akun' => '2', 'saldo_akhir' => 11195625, 'tahun' => 2024],
            ['id_akun' => '3', 'saldo_akhir' => 0, 'tahun' => 2024],

            // Bank
            ['id_akun' => '4', 'saldo_akhir' => 1442013107, 'tahun' => 2024],
            ['id_akun' => '5', 'saldo_akhir' => 222870259, 'tahun' => 2024],
            ['id_akun' => '6', 'saldo_akhir' => 92383185, 'tahun' => 2024],
            ['id_akun' => '7', 'saldo_akhir' => 6703583, 'tahun' => 2024],
            ['id_akun' => '8', 'saldo_akhir' => 192813746, 'tahun' => 2024],
            ['id_akun' => '9', 'saldo_akhir' => 361584915, 'tahun' => 2024],
            ['id_akun' => '10', 'saldo_akhir' => 80294498, 'tahun' => 2024],
            ['id_akun' => '11', 'saldo_akhir' => 137806480, 'tahun' => 2024],
            ['id_akun' => '12', 'saldo_akhir' => 25491353, 'tahun' => 2024],
            ['id_akun' => '13', 'saldo_akhir' => 185060535, 'tahun' => 2024],

            // Persediaan
            ['id_akun' => '14', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '15', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '16', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '17', 'saldo_akhir' => 0, 'tahun' => 2024],
            
            // Piutang
            ['id_akun' => '18', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '19', 'saldo_akhir' => 87954425, 'tahun' => 2024],
            ['id_akun' => '20', 'saldo_akhir' => 60700000, 'tahun' => 2024],
            ['id_akun' => '21', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '22', 'saldo_akhir' => 0, 'tahun' => 2024],
            
            // Aset Lancar Lainnya
            ['id_akun' => '23', 'saldo_akhir' => 106944448, 'tahun' => 2024],
            ['id_akun' => '24', 'saldo_akhir' => 1083309064, 'tahun' => 2024],
            ['id_akun' => '25', 'saldo_akhir' => 3549832, 'tahun' => 2024],
            ['id_akun' => '26', 'saldo_akhir' => 0, 'tahun' => 2024],
            
            // Aktiva Tetap
            ['id_akun' => '27', 'saldo_akhir' => 21050868400, 'tahun' => 2024],
            ['id_akun' => '28', 'saldo_akhir' => 39788797721, 'tahun' => 2024],
            ['id_akun' => '29', 'saldo_akhir' => 346773000, 'tahun' => 2024],
            ['id_akun' => '30', 'saldo_akhir' => 1924747193, 'tahun' => 2024],
            ['id_akun' => '31', 'saldo_akhir' => 1140330600, 'tahun' => 2024],
            ['id_akun' => '32', 'saldo_akhir' => 369455350, 'tahun' => 2024],
            ['id_akun' => '33', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '34', 'saldo_akhir' => -15904586925, 'tahun' => 2024],
            ['id_akun' => '35', 'saldo_akhir' => -1191112426, 'tahun' => 2024],
            ['id_akun' => '36', 'saldo_akhir' => -615069791, 'tahun' => 2024],
            ['id_akun' => '37', 'saldo_akhir' => -255342850, 'tahun' => 2024],
            ['id_akun' => '38', 'saldo_akhir' =>  0, 'tahun' => 2024],








            // --------------------------------------KEWAJIBAN
            // Kewajiban Jangka Pendek
            ['id_akun' => '39', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '40', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '41', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '42', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '43', 'saldo_akhir' => 1083309065, 'tahun' => 2024],
            ['id_akun' => '44', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '45', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '46', 'saldo_akhir' => 5345600, 'tahun' => 2024],
            ['id_akun' => '47', 'saldo_akhir' => 1272364960, 'tahun' => 2024],
            ['id_akun' => '48', 'saldo_akhir' => 30850000, 'tahun' => 2024],
            ['id_akun' => '49', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '50', 'saldo_akhir' => 6465782, 'tahun' => 2024],

            // Kewajiban Jangka Panjang
            ['id_akun' => '51', 'saldo_akhir' => 4270963737, 'tahun' => 2024],
            ['id_akun' => '52', 'saldo_akhir' => 2050000000, 'tahun' => 2024],
            ['id_akun' => '53', 'saldo_akhir' => 0, 'tahun' => 2024],

            









            // --------------------------------------ASET NETO
            ['id_akun' => '54', 'saldo_akhir' => 2809987526, 'tahun' => 2024],
            ['id_akun' => '55', 'saldo_akhir' => 39279925111, 'tahun' => 2024],











            // --------------------------------------PENERIMAAN DAN SUMBANGAN
            // Penerimaan dan Sumbangan Pendidikan
            ['id_akun' => '56', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '57', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '58', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '59', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '60', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '61', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '62', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '63', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '64', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '65', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '66', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '67', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '68', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '69', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '70', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '71', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '72', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '73', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '74', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '75', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '76', 'saldo_akhir' => 0, 'tahun' => 2024],


            // Penerimaan dan Sumbangan Non Pendidikan
            ['id_akun' => '77', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '78', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '79', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '80', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '81', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '82', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '83', 'saldo_akhir' => 0, 'tahun' => 2024],








            // --------------------------------------BEBAN
            //Beban Operasional
            ['id_akun' => '84', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '85', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '86', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '87', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '88', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '89', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '90', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '91', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '92', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '93', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '94', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '95', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '96', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '97', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '98', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '99', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '100', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '101', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '102', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '103', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '104', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '105', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '106', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '107', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '108', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '109', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '110', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '111', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '112', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '113', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '114', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '115', 'saldo_akhir' => 0, 'tahun' => 2024],

            //Beban Non Operasional
            ['id_akun' => '116', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '117', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '118', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '119', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '120', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '121', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '122', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '123', 'saldo_akhir' => 0, 'tahun' => 2024],
            ['id_akun' => '124', 'saldo_akhir' => 0, 'tahun' => 2024],

        ];

        foreach ($akun as $data) {
            Saldo_Akhir_Tahun::create($data); 
        }
    }
}
