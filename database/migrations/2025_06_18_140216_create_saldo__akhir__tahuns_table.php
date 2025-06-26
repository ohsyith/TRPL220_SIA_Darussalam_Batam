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
        Schema::create('saldo_akhir_tahun', function (Blueprint $table) {
            $table->bigIncrements('id_saldo_akhir_tahun');
            $table->unsignedBigInteger('id_akun');
            $table->bigInteger('saldo_akhir')->default(0);
            $table->integer('tahun');

            $table->timestamps();

            $table->foreign('id_akun')->references('id_akun')->on('akun')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldo__akhir__tahuns');
    }
};
