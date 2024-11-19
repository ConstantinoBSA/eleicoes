<?php

namespace App\Core;

class Validator {
    protected $errors = [];
    protected $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function validate($data, $rules) {
        foreach ($rules as $field => $ruleSet) {
            $rulesArray = explode('|', $ruleSet);
            foreach ($rulesArray as $rule) {
                $parameters = [];
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramString) = explode(':', $rule);
                    $parameters = explode(',', $paramString);
                }
                $method = 'validate' . ucfirst($rule);
                if (method_exists($this, $method)) {
                    $this->$method($field, $data[$field] ?? null, $parameters);
                }
            }
        }
        return $this->errors;
    }

    protected function validateRequired($field, $value) {
        if (empty($value) && $value == null) {
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
}
