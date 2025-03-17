<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurnal_Umum extends Model
{
    use HasFactory;
    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal_umum';

    protected $fillable = ['tanggal', 'no_bukti', 'keterangan', 'jenis', 'id_unit', 'id_devisi', 'kode_sumbangan', 'kode_ph'];

}
