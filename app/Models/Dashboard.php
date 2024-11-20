<?php

namespace App\Models;

use App\Core\Model;

class Dashboard extends Model
{
    public function __construct()
    {
        parent::__construct(); // Garante que a conexão com o banco de dados seja estabelecida
    }

    public function escolas() 
    {
        $sql = "
            SELECT e.nome AS escola_nome, ca.nome AS candidato_nome, cc.cargo, ch.nome AS chapa_nome
            FROM candidatos ca
            JOIN escolas e ON ca.escola_id = e.id
            JOIN chapa_candidatos cc ON ca.id = cc.candidato_id
            JOIN chapas ch ON cc.chapa_id = ch.id
            ORDER BY e.id, ch.nome
        ";

        $stmt = $this->pdo->query($sql);
        $escolas = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $escolas[$row['escola_nome']][] = [
                'candidato_nome' => $row['candidato_nome'],
                'cargo' => $row['cargo'],
                'chapa' => $row['chapa_nome'],
            ];
        }

        return $escolas;
    }

    public function eleitores()
    {
        $sql_eleitores = "
            SELECT e.nome AS escola_nome, s.nome AS segmento_nome, COUNT(el.id) AS total_eleitores
            FROM eleitores el
            JOIN escolas e ON el.escola_id = e.id
            JOIN segmentos s ON el.segmento_id = s.id
            GROUP BY e.id, s.id
            ORDER BY e.id, s.id
        ";

        $stmt_eleitores = $this->pdo->query($sql_eleitores);
        $eleitores = [];

        while ($row = $stmt_eleitores->fetch(\PDO::FETCH_ASSOC)) {
            $eleitores[$row['escola_nome']][$row['segmento_nome']] = $row['total_eleitores'];
        }

        return $eleitores;
    }

    public function totalEleitoresPorEscola($school) 
    {
        $total = 0;
        foreach ($school as $count) {
            $total += $count;
        }
        return $total;
    }
}
