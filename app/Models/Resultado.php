<?php

namespace App\Models;

use App\Core\Model;

class Resultado extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function gerarResultados() {
        // Consulta para obter os resultados das chapas agrupados por escola, incluindo total de eleitores e votos
        $sql = "
            SELECT 
                e.nome AS escola_nome, 
                ch.nome AS chapa_nome, 
                COUNT(r.id) AS votos,
                (SELECT COUNT(*) FROM eleitores el WHERE el.escola_id = e.id) AS total_eleitores
            FROM resultados r
            JOIN chapas ch ON r.chapa_id = ch.id
            JOIN escolas e ON ch.escola_id = e.id
            GROUP BY ch.id, e.id
            ORDER BY e.nome, ch.nome
        ";
    
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
            // Estruturar os resultados por escola
            $resultadoPorEscola = [];
            foreach ($resultados as $resultado) {
                $escolaNome = $resultado['escola_nome'];
                $totalEleitores = $resultado['total_eleitores'];
    
                // Inicialize o total de votos na escola, se ainda não feito
                if (!isset($resultadoPorEscola[$escolaNome]['total_votos'])) {
                    $resultadoPorEscola[$escolaNome]['total_votos'] = 0;
                }
    
                // Acumula votos totais na escola
                $resultadoPorEscola[$escolaNome]['total_votos'] += $resultado['votos'];
                
                // Calcula a porcentagem de votos da chapa
                $porcentagemVotosChapa = ($totalEleitores > 0) ? ($resultado['votos'] / $totalEleitores) * 100 : 0;
                
                // Armazena dados da chapa
                $resultadoPorEscola[$escolaNome]['chapas'][] = [
                    'chapa_nome' => $resultado['chapa_nome'],
                    'votos' => $resultado['votos'],
                    'porcentagem_votos' => $porcentagemVotosChapa
                ];
                
                // Armazena total de eleitores
                $resultadoPorEscola[$escolaNome]['total_eleitores'] = $totalEleitores;
            }
    
            return $resultadoPorEscola;
        } catch (PDOException $e) {
            throw new Exception("Erro ao gerar resultados: " . $e->getMessage());
        }
    }
}
