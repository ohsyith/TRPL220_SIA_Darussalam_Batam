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
        Schema::create('akuntan_divisi', function (Blueprint $table) {
            $table->unsignedBigInteger('id_akuntan_divisi')->primary(); // <-- Jadi primary key tapi bukan auto increment

            $table->string('email')->unique();;
            $table->string('telp')->unique();;

            $table->unsignedBigInteger('id_divisi');
            $table->foreign('id_divisi')->references('id_divisi')->on('divisi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akuntan__divisis');
    }
};
