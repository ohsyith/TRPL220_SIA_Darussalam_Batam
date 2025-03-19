<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Divisi;
use App\Models\Jenis_Transaksi;
use App\Models\Detail_Jurnal_Umum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurnal_Umum extends Model
{
    use HasFactory;
    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal_umum';

    protected $fillable = ['tanggal', 'no_bukti', 'keterangan', 'id_jenis_transaksi', 'id_unit', 'id_divisi', 'kode_sumbangan', 'kode_ph'];


    public function jenis_transaksi(): BelongsTo
    {
        return $this->belongsTo(Jenis_Transaksi::class, 'id_jenis_transaksi', 'id_jenis_transaksi');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id_unit');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function detail_jurnal_umum(): HasMany
    {
        return $this->hasMany(Detail_Jurnal_Umum::class, 'id_jurnal_umum', 'id_jurnal_umum');
    }

}
