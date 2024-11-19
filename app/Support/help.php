<?php

use App\Core\Database;

function generateSaleNumber()
{
    // Lógica para gerar um número de venda único
    // Exemplo: concatenar ano atual com um ID autoincremental ou outro identificador único
    $pdo = Database::getInstance()->getConnection();
    $stmt = $pdo->query("SELECT MAX(id) AS max_id FROM vendas");
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    $nextId = $row ? $row['max_id'] + 1 : 1;
    $currentYear = date('Y');
    return "{$currentYear}-{$nextId}";
}

function generateProductNumber($productName)
{
    // Lista de palavras a serem ignoradas
    $ignoreWords = ['de', 'da', 'do', 'dos', 'e', 'das'];

    // Separar o nome do produto em palavras
    $words = explode(' ', $productName);
    
    // Filtrar palavras ignoradas e pegar as iniciais das duas primeiras palavras significativas
    $initials = '';
    foreach ($words as $word) {
        if (!in_array(strtolower($word), $ignoreWords)) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) == 2) {
                break;
            }
        }
    }

    // Verificar se conseguimos duas iniciais, caso contrário, preencher com 'X'
    $initials = str_pad($initials, 2, 'X');

    // Conectar ao banco de dados para obter o próximo número do produto
    $pdo = Database::getInstance()->getConnection();

    // Obter o maior número sequencial para essas iniciais
    $stmt = $pdo->prepare("SELECT codigo FROM produtos WHERE codigo LIKE :initials ORDER BY codigo DESC LIMIT 1");
    $stmt->execute([':initials' => $initials . '%']);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($row) {
        // Extrair o número atual e incrementar
        $currentNumber = (int)substr($row['codigo'], 2);
        $nextNumber = $currentNumber + 1;
    } else {
        // Se não existirem produtos, iniciar com 1
        $nextNumber = 1;
    }

    // Formatar o número sequencial para 5 dígitos, preenchendo com zeros à esquerda
    $sequenceNumber = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    // Retornar o número do produto no formato LL99999
    return "{$initials}{$sequenceNumber}";
}

