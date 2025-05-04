<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log_Activity extends Model
{
    use HasFactory;
    protected $table = 'log_activity';
    protected $primaryKey = 'id_log_activity';
}
