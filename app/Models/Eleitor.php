<?php

namespace App\Models;

use App\Core\Model;

class Eleitor extends Model
{
    protected $tableName = 'eleitores';
    protected $fillable = [
        'id',
        'nome',
        'segmento_id',
        'documento',
        'registrado',
        'escola_id',
        'status',
        'created_at',
        'updated_at'
    ];

    public function segmento()
    {
        return $this->belongsTo(Segmento::class, 'segmento_id', 'id');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'escola_id', 'id');
    }
}
