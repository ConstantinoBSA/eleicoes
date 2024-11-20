<?php

namespace App\Models;

use App\Core\Model;

class Cedula extends Model
{
    protected $tableName = 'cedulas';
    protected $fillable = [
        'id',
        'codigo_seguranca',
        'escola_id',
        'eleitor_id',
        'usado',
        'tipo',
        'data_emissao',
        'created_at',
        'updated_at'
    ];

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'escola_id', 'id');
    }
    
    public function mostrarCedulas()
    {
        // Busca as cédulas associadas a uma escola e conta o total por escola
        $stmt = $this->pdo->prepare("
            SELECT escolas.id AS escola_id, escolas.nome AS escola_nome, 
                COUNT(cedulas.id) AS total_cedulas
            FROM cedulas
            JOIN escolas ON cedulas.escola_id = escolas.id
            GROUP BY escolas.id, escolas.nome
        ");
        $stmt->execute();
        $cedulasPorEscola = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $cedulasPorEscola;
    }
}
