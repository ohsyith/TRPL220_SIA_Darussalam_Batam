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
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->bigIncrements('id_jurnal_umum');
            $table->date('tanggal');
            $table->string('no_bukti');
            $table->string('keterangan');

            $table->unsignedBigInteger('id_jenis_transaksi');
            $table->foreign('id_jenis_transaksi')->references('id_jenis_transaksi')->on('jenis_transaksi');
            
            $table->unsignedBigInteger('id_unit');
            $table->foreign('id_unit')->references('id_unit')->on('unit');

            $table->unsignedBigInteger('id_divisi');
            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');

            $table->string('kode_sumbangan');
            $table->string('kode_ph');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_umum');
    }
};
