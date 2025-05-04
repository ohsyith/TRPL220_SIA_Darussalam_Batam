<?php

namespace App\Models;

use App\Models\Sub_Kategori_Akun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akun extends Model
{
    use HasFactory;
    protected $table = 'akun';

    protected $primaryKey = 'id_akun';
    protected $fillable = ['id_sub_kategori_akun', 'kode_akun', 'akun', 'saldo_awal_debit', 'saldo_awal_kredit', 'budget_rapbs'];


    public function sub_kategori_akun(): BelongsTo
    {
        return $this->belongsTo(Sub_Kategori_Akun::class, 'id_sub_kategori_akun', 'id_sub_kategori_akun');
    }
}
