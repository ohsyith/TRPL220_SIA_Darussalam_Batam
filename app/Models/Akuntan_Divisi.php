<?php

namespace App\Models;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akuntan_Divisi extends Model
{
    use HasFactory;
    protected $table = 'akuntan_divisi';
    protected $primaryKey = 'id_akuntan_divisi';
    protected $fillable = ['id_akuntan_divisi', 'id_divisi', 'email', 'telp'];

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_akuntan_divisi', 'id_user');
    }
}
