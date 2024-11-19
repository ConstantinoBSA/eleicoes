<?php

namespace App\Models;

use App\Core\Model;

class Produto extends Model
{
    protected $tableName = 'produtos';
    protected $fillable = [
        'id',
        'nome',
        'codigo',
        'descricao',
        'preco',
        'preco_promocional',
        'codigo_barras',
        'estoque',
        'slug',
        'imagem',
        'categoria_id',
        'informacoes_relevantes',
        'data_lancamento',
        'pontos',
        'promocao',
        'destaque',
        'status',
        'created_at',
        'updated_at'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }
}
