<?php

namespace App\Core;

class Validator {
    protected array $data = []; 
    protected $errors = [];
    protected $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function validate(array $data, array $rules): array {
        $this->data = $data; // Inicializa a propriedade com os dados de entrada
        $this->errors = []; // Assegura que os erros estão vazios
        

        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            $value = $this->data[$field] ?? null;

            if (is_array($value)) {
                foreach ($value as $index => $item) {
                    $this->applyRules($field, $item, $rulesArray, $index);
                }
            } else {
                $this->applyRules($field, $value, $rulesArray);
            }
        }
        return $this->errors;
    }

    protected function applyRules(string $field, $value, array $rulesArray, $index = null): void {
        foreach ($rulesArray as $rule) {
            $parameters = [];
            if (strpos($rule, ':') !== false) {
                list($rule, $parameterString) = explode(':', $rule);
                $parameters = explode(',', $parameterString);
            }

            $methodName = 'validate' . ucfirst($rule);
            if (method_exists($this, $methodName)) {
                if ($index !== null) {
                    $fieldWithIndex = "{$field}.{$index}";
                    $this->$methodName($fieldWithIndex, $value, $parameters);
                } else {
                    $this->$methodName($field, $value, $parameters);
                }
            } else {
                $this->errors[$field][] = "Método de validação '{$rule}' não encontrado.";
            }
        }
    }

    protected function validateRequired($field, $value) {
        if (empty($value)) {
            $this->errors[$field] = 'O campo ' . $field . ' é obrigatório.';
        }
    }

    protected function validateEmail($field, $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'O campo ' . $field . ' deve ser um email válido.';
        }
    }

    protected function validateUnique($field, $value, $parameters) {
        if (count($parameters) < 1) {
            $this->errors[$field] = 'Erro de validação: parâmetros insuficientes para a validação única.';
            return;
        }

        $table = $parameters[0];
        $column = $parameters[1] ?? $field; // Use o campo como coluna padrão se não especificado
        $excludeId = $parameters[2] ?? null;

        if ($this->pdo) {
            $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
            if ($excludeId) {
                $sql .= " AND id != :excludeId";
            }
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':value', $value);
            if ($excludeId) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            $stmt->execute();
            $count = $stmt->fetchColumn();
            if ($count > 0) {
                $this->errors[$field] = 'O valor do campo ' . $field . ' já está em uso.';
            }
        } else {
            $this->errors[$field] = 'Erro de validação: conexão com o banco de dados não configurada.';
        }
    }

    protected function validateUniqueCombination($field, $value, $parameters) {
        if (count($parameters) < 2) {
            $this->errors[$field] = 'Erro de validação: parâmetros insuficientes para a validação de combinação única.';
            return;
        }
    
        $table = array_shift($parameters);
        $excludeId = isset($parameters[count($parameters) - 2]) ? end($parameters) : null;
        if (is_numeric($excludeId)) {
            array_pop($parameters);
        } else {
            $excludeId = null;
        }
    
        $conditions = ["$field = :$field"];
        $bindings = [":$field" => $value];
    
        // Iterar sobre os pares campo-valor adicionais
        foreach (array_chunk($parameters, 2) as $pair) {
            if (count($pair) == 2) {
                list($otherColumn, $otherFieldName) = $pair;
                if (isset($this->data[$otherFieldName])) {
                    $conditions[] = "$otherColumn = :$otherColumn";
                    $bindings[":$otherColumn"] = $this->data[$otherColumn];
                } else {
                    $this->errors[$field] = "O campo '$otherFieldName' não está presente nos dados.";
                    return;
                }
            }
        }
    
        if ($excludeId) {
            $conditions[] = "id != :excludeId";
            $bindings[':excludeId'] = $excludeId;
        }
    
        $sql = "SELECT COUNT(*) FROM $table WHERE " . implode(' AND ', $conditions);
    
        if ($this->pdo) {
            $stmt = $this->pdo->prepare($sql);
            foreach ($bindings as $param => $val) {
                $stmt->bindValue($param, $val);
            }
            $stmt->execute();
            $count = $stmt->fetchColumn();
    
            if ($count > 0) {
                $this->errors[$field] = 'A combinação dos valores para ' . $field . ' e campos associados já está em uso.';
            }
        } else {
            $this->errors[$field] = 'Erro de validação: conexão com o banco de dados não configurada.';
        }
    }

    protected function validateMin($field, $value, $parameters) {
        $min = $parameters[0];
        if (strlen($value) < $min) {
            $this->errors[$field] = 'O campo ' . $field . ' deve ter pelo menos ' . $min . ' caracteres.';
        }
    }

    protected function validateMax($field, $value, $parameters) {
        $max = $parameters[0];
        if (strlen($value) > $max) {
            $this->errors[$field] = 'O campo ' . $field . ' não pode ter mais de ' . $max . ' caracteres.';
        }
    }

    protected function validateNumeric($field, $value) {
        if (!is_numeric($value)) {
            $this->errors[$field] = 'O campo ' . $field . ' deve ser numérico.';
        }
    }

    protected function validateArray($field, $value) {
        if (!is_array($value)) {
            $this->errors[$field] = 'O campo ' . $field . ' deve ser um array.';
        }
    }
}
