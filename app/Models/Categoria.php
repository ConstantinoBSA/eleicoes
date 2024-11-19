<?php

namespace App\Models;

use App\Core\Model;

class Categoria extends Model
{
    protected $tableName = 'categorias';
    protected $fillable = [
        'id',
        'nome',
        'slug',
        'status',
        'created_at',
        'updated_at'
    ];
}
