<?php

namespace App\Models;

use App\Core\Model;

class Candidato extends Model
{
    protected $tableName = 'candidatos';
    protected $fillable = [
        'id',
        'nome',
        'escola_id',
        'status',
        'created_at',
        'updated_at'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'escola_id', 'id');
    }
}
