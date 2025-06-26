<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget_Rapbs_Akun extends Model
{
    use HasFactory;
    protected $table = 'budget_rapbs_akun';
    protected $primaryKey = 'id_budget_rapbs_akun';
    protected $fillable = ['id_akun', 'id_unit', 'budget_rapbs_akun'];
}
