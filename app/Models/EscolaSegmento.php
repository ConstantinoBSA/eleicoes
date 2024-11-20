<?php

namespace App\Models;

use App\Core\Model;

class EscolaSegmento extends Model
{
    protected $tableName = 'escola_segmentos';
    protected $primaryKey = false;
    protected $fillable = [
        'nome',
        'status',
        'created_at',
        'updated_at'
    ];

    public function segmento()
    {
        return $this->belongsTo(Segmento::class, 'segmento_id', 'id');
    }
}
