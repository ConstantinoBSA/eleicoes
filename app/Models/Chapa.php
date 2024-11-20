<?php

namespace App\Models;

use App\Core\HasPivot;
use App\Core\Model;

class Chapa extends Model
{
    use HasPivot;

    protected $tableName = 'chapas';
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

    public function candidatos()
    {
        return $this->belongsToMany(Candidato::class, 'chapa_candidatos', 'chapa_id', 'candidato_id', 'id', 'id');
    }
}
