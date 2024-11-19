<?php

namespace App\Models;

use App\Core\Model;

class Cedula extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function gerarEExibirCedulas($escolaId) 
    {
        // Obtém o total de eleitores para a escola
        $stmt = $this->pdo->prepare("SELECT COUNT(id) FROM eleitores WHERE escola_id = :escola_id");
        $stmt->execute(['escola_id' => $escolaId]);
        $numeroDeEleitores = $stmt->fetchColumn();

        if ($numeroDeEleitores > 0) {
            // Gera cédulas correspondentes ao número de eleitores
            $stmtInsert = $this->pdo->prepare("INSERT INTO cedulas (codigo_seguranca, escola_id) VALUES (:codigo, :escola_id)");
            $stmtSelect = $this->pdo->prepare("SELECT codigo_seguranca FROM cedulas WHERE escola_id = :escola_id");

            // Geração e inserção das cédulas
            for ($i = 0; $i < $numeroDeEleitores; $i++) {
                $codigoSeguranca = gerarCodigoSeguranca();
                $stmtInsert->execute(['codigo' => $codigoSeguranca, 'escola_id' => $escolaId]);
            }

            // Seleciona todas as cédulas geradas para exibição
            $stmtSelect->execute(['escola_id' => $escolaId]);
            $cedulas = $stmtSelect->fetchAll(PDO::FETCH_ASSOC);

            echo "<h4>Cédulas Geradas para a Escola ID: $escolaId</h4>";
            echo "<p>Total de cédulas: " . count($cedulas) . "</p>";
            echo "<ul>";
            foreach ($cedulas as $cedula) {
                echo "<li>Código de Segurança: " . htmlspecialchars($cedula['codigo_seguranca'], ENT_QUOTES, 'UTF-8') . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum eleitor registrado para a escola ID: $escolaId.</p>";
        }
    }
}
