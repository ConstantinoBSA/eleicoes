<?php

namespace App\Models;

use App\Core\Model;

class Configuracao extends Model
{
    public function getConfiguracao($chave)
    {
        $stmt = $this->pdo->prepare("SELECT valor FROM configuracoes WHERE chave = :chave");
        $stmt->execute(['chave' => $chave]);
        return $stmt->fetchColumn();
    }

    public function getAllConfiguracoes()
    {
        $stmt = $this->pdo->query("SELECT chave, valor FROM configuracoes");
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Converte o resultado em um array associativo chave => valor
        $configArray = [];
        foreach ($result as $config) {
            $configArray[$config['chave']] = $config['valor'];
        }
        
        return $configArray;
    }

    public function setConfiguracao($chave, $valor)
    {
        try {
            $sql = "INSERT INTO configuracoes (chave, valor) 
                    VALUES (:chave, :valor) 
                    ON DUPLICATE KEY UPDATE valor = :update_valor";
    
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                'chave' => $chave,
                'valor' => $valor,
                'update_valor' => $valor
            ]);
    
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('SQL Error: ' . $errorInfo[2]);
            }
    
        } catch (Exception $e) {
            // Log or handle the error
            echo "Failed to update configuration: " . $e->getMessage();
        }
    }

    public function deleteConfiguracao($chave)
    {
        $stmt = $this->pdo->prepare("DELETE FROM configuracoes WHERE chave = :chave");
        return $stmt->execute(['chave' => $chave]);
    }
}
