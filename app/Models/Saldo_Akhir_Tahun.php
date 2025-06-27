<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saldo_Akhir_Tahun extends Model
{
    use HasFactory;
    protected $table = 'saldo_akhir_tahun';

    protected $primaryKey = 'id_saldo_akhir_tahun';
    protected $fillable = ['id_akun', 'saldo_akhir', 'tahun'];

}
