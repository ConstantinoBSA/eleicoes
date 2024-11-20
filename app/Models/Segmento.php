<?php

namespace App\Models;

use App\Core\Model;

class Segmento extends Model
{
    protected $tableName = 'segmentos';
    protected $fillable = [
        'nome',
        'status',
        'created_at',
        'updated_at'
    ];
}
