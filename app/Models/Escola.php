<?php

namespace App\Models;

use App\Core\Model;

class Escola extends Model
{
    protected $tableName = 'escolas';
    protected $fillable = [
        'id',
        'nome',
        'sigla',
        'endereco',
        'status',
        'created_at',
        'updated_at'
    ];

    public function chapas()
    {
        return $this->hasMany(Chapa::class, 'escola_id', 'id');
    }

    public function segmentos()
    {
        return $this->belongsToMany(Segmento::class, 'escola_segmentos', 'escola_id', 'segmento_id', 'id', 'id');
    }
}
