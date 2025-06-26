<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Komprehensif_Akhir_Tahun extends Model
{
    use HasFactory;
    protected $table = 'komprehensif_akhir_tahun';

    protected $primaryKey = 'id_komprehensif_akhir_tahun';
    protected $fillable = ['id_akun', 'saldo_akhir_dengan_pembatasan', 'saldo_akhir_tanpa_pembatasan', 'tahun'];

}
