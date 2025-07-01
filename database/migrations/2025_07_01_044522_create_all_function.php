<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // DB::unprepared("
        //     DROP FUNCTION IF EXISTS get_kenaikan_aset_neto;

        //     CREATE FUNCTION get_kenaikan_aset_neto(
        //         p_id_akun INT,
        //         p_start_date DATE,
        //         p_end_date DATE,
        //         p_id_unit INT,
        //         p_id_divisi INT
        //     ) RETURNS JSON
        //     DETERMINISTIC
        //     READS SQL DATA
        //     BEGIN
        //         DECLARE total_debit DECIMAL(15,2) DEFAULT 0;
        //         DECLARE total_kredit DECIMAL(15,2) DEFAULT 0;
                
        //         SELECT 
        //             COALESCE(SUM(CASE WHEN dju.debit_kredit = 'debit' THEN dju.nominal ELSE 0 END), 0),
        //             COALESCE(SUM(CASE WHEN dju.debit_kredit = 'kredit' THEN dju.nominal ELSE 0 END), 0)
        //         INTO total_debit, total_kredit
        //         FROM detail_jurnal_umum dju
        //         JOIN jurnal_umum ju ON dju.id_jurnal_umum = ju.id_jurnal_umum
        //         JOIN buku_besar bb ON ju.id_jurnal_umum = bb.id_jurnal_umum
        //         WHERE dju.id_akun = p_id_akun
        //         AND ju.tanggal BETWEEN p_start_date AND p_end_date
        //         AND (p_id_unit IS NULL OR ju.id_unit = p_id_unit)
        //         AND (p_id_divisi IS NULL OR ju.id_divisi = p_id_divisi);
                
        //         RETURN JSON_OBJECT(
        //             'total_debit', total_debit,
        //             'total_kredit', total_kredit
        //         );
        //     END;
        // ");

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_function');
    }
};
