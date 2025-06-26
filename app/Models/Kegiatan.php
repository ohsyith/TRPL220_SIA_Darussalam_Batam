<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Jurnal_Umum;
use App\Models\Detail_Jurnal_Umum;

class Kegiatan extends Model
{
    use HasFactory;
    protected $table = 'kegiatan';
    protected $primaryKey = 'id_kegiatan';
    protected $fillable = ['kode_kegiatan', 'kegiatan', 'budget_rapbs', 'id_unit'];

    public function jurnal_umum()
    {
        return $this->hasMany(Jurnal_Umum::class, 'id_kegiatan', 'id_kegiatan');
    }

}
