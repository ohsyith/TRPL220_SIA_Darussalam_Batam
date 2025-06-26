<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jenis_Transaksi extends Model
{
    use HasFactory;
    protected $table = 'jenis_transaksi';
    protected $primaryKey = 'id_jenis_transaksi';
}
