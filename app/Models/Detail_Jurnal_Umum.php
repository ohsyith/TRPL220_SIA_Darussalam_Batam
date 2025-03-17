<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detail_Jurnal_Umum extends Model
{
    use HasFactory;
    protected $table = 'detail_jurnal_umum';
    protected $primaryKey = 'id_detail_jurnal_umum';

    protected $fillable = ['id_jurnal_umum', 'id_akun', 'nominal', 'debit_kredit'];
}
