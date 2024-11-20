<?php

namespace App\Core;

use InvalidArgumentException;
use PDO;

class Model
{
    protected $pdo;
    protected $tableName;
    protected $query;
    protected $bindings = [];
    protected $primaryKey = 'id';
    protected $selectColumns = '*';
    protected $fillable = [];
    protected $attributes = [];

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->resetQuery();
    }

    public function table($tableName)
    {
        if (!is_string($tableName) || empty($tableName)) {
            throw new InvalidArgumentException("O nome da tabela deve ser uma string não vazia.");
        }
        $this->tableName = $tableName;
        return $this; // Permite o encadeamento de métodos
    }

    public function getTableName()
    {
        if (empty($this->tableName)) {
            throw new \Exception("Nome da tabela não foi definido.");
        }
        return $this->tableName;
    }

     /**
     * Define a chave primária para o modelo.
     */
    public function setPrimaryKey($key)
    {
        if (!is_string($key) || empty($key)) {
            throw new InvalidArgumentException("A chave primária deve ser uma string não vazia.");
        }
        $this->primaryKey = $key;
        return $this;
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable) || $key === $this->primaryKey) {
                $this->$key = $value;
            }
        }
    }

    public function select(...$columns)
    {
        $this->selectColumns = implode(', ', $columns);
        $this->query = "SELECT {$this->selectColumns} FROM {$this->tableName}";
        return $this;
    }

    public function distinct()
    {
        $this->query = str_replace('SELECT', 'SELECT DISTINCT', $this->query);
        return $this;
    }

    public function groupBy(...$columns)
    {
        $this->query .= ' GROUP BY ' . implode(', ', $columns);
        return $this;
    }

    public function where($column, $operator = '=', $value = null, $boolean = 'AND')
    {
        // Verificar se o valor é nulo e ajustar o operador adequadamente
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        // Validar o operador
        $this->validateOperator($operator);

        // Ajustar para operador LIKE
        if ($operator === 'LIKE') {
            $value = '%' . $value . '%';
        }

        // Gerar um nome único para o parâmetro (para evitar conflitos)
        $paramName = ':' . str_replace('.', '_', $column) . count($this->bindings);

        // Adicionar o valor à matriz de bindings
        $this->bindings[$paramName] = $value;

        // Construção da cláusula WHERE
        if (empty($this->query) || stripos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE $column $operator $paramName";
        } else {
            $this->query .= " $boolean $column $operator $paramName";
        }

        return $this;
    }

    public function orWhere($column, $operator = '=', $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function orderBy($column, $direction = 'ASC')
    {
        // Validação simples da direção de ordenação
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'])) {
            throw new InvalidArgumentException('O parâmetro de direção deve ser "ASC" ou "DESC".');
        }

        // Adicionar a cláusula ORDER BY à consulta
        if (stripos($this->query, 'ORDER BY') === false) {
            $this->query .= " ORDER BY $column $direction";
        } else {
            $this->query .= ", $column $direction";
        }

        return $this;
    }

    public function first()
    {
        $this->query .= ' LIMIT 1';
        $stmt = $this->executeQuery();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $object = new static();
            $object->fillFromDatabase($result);
            return $object;
        }

        return null;
    }

    public function innerJoin($table, $first, $operator, $second)
    {
        $this->query .= " INNER JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin($table, $first, $operator, $second)
    {
        $this->query .= " LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin($table, $first, $operator, $second)
    {
        $this->query .= " RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function fullJoin($table, $first, $operator, $second)
    {
        $this->query .= " FULL JOIN $table ON $first $operator $second";
        return $this;
    }

    public function get()
    {
        $stmt = $this->executeQuery();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($results as $result) {
            $object = new static();
            $object->fillFromDatabase($result); // Preenche usando o resultado do banco de dados
            $objects[] = $object;
        }

        return $objects;
    }

    public function paginate($perPage, $currentPage)
    {
        $offset = ($currentPage - 1) * $perPage;
        $paginatedQuery = $this->query . " LIMIT :limit OFFSET :offset";

        // Preparar a consulta de forma segura
        $stmt = $this->pdo->prepare($paginatedQuery);

        // Vincular os parâmetros nomeados
        foreach ($this->bindings as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        // Vincular os parâmetros de paginação
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($results as $result) {
            $object = new static();
            $object->fillFromDatabase($result);
            $objects[] = $object;
        }

        $totalRecords = $this->count(); // Assumindo que este método conta todos os registros
        
        return new PaginatedCollection($objects, [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total_records' => $totalRecords,
            'total_pages' => ceil($totalRecords / $perPage),
        ]);
    }

    public function count()
    {
        $this->query = str_replace('SELECT *', 'SELECT COUNT(*) as count', $this->query);
        return $this->executeQuery()->fetchColumn();
    }

    public function sum($column)
    {
        return $this->aggregate('SUM', $column);
    }

    public function avg($column)
    {
        return $this->aggregate('AVG', $column);
    }

    public function min($column)
    {
        return $this->aggregate('MIN', $column);
    }

    public function max($column)
    {
        return $this->aggregate('MAX', $column);
    }

    private function aggregate($function, $column)
    {
        $query = str_replace('SELECT *', "SELECT {$function}({$column}) as aggregate", $this->query);
        return $this->executeQuery($query)->fetchColumn();
    }

    private function executeQuery($query = null)
    {
        if ($query === null) {
            $query = $this->query;
        }

        // Verificação adicional para evitar consultas vazias
        if (empty(trim($query))) {
            throw new \Exception("A query SQL está vazia. Certifique-se de construir a query antes de executá-la.");
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($this->bindings);
        return $stmt;
    }

    public function hasOne($relatedClass, $foreignKey, $localKey)
    {
        $instance = new $relatedClass($this->pdo);
        $query = "SELECT * FROM {$instance->getTableName()} WHERE {$foreignKey} = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$this->$localKey]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $object = new $relatedClass();
            $object->fillFromDatabase($result);
            return $object;
        }

        return null;
    }

    public function hasMany($relatedClass, $foreignKey, $localKey)
    {
        $instance = new $relatedClass($this->pdo);
        $query = "SELECT * FROM {$instance->getTableName()} WHERE {$foreignKey} = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$this->$localKey]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($results as $result) {
            $object = new $relatedClass();
            $object->fillFromDatabase($result);
            $objects[] = $object;
        }

        return $objects;
    }

    public function belongsTo($relatedClass, $foreignKey, $ownerKey)
    {
        $instance = new $relatedClass($this->pdo);
        $query = "SELECT * FROM {$instance->getTableName()} WHERE {$ownerKey} = ? LIMIT 1";
        $stmt = $this->pdo->prepare($query);

        $stmt->execute([$this->$foreignKey]); // Usa o valor da chave estrangeira para executar a consulta

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $object = new $relatedClass();
            $object->fillFromDatabase($result); // Preenche o objeto relacionado após obter os dados
            return $object;
        }

        return null;
    }

    public function belongsToMany($relatedClass, $pivotTable, $foreignKey, $relatedKey, $localKey, $relatedLocalKey)
    {
        $instance = new $relatedClass($this->pdo);
        $query = "SELECT {$instance->getTableName()}.*, {$pivotTable}.*
                  FROM {$instance->getTableName()}
                  JOIN {$pivotTable} ON {$instance->getTableName()}.{$relatedLocalKey} = {$pivotTable}.{$relatedKey}
                  WHERE {$pivotTable}.{$foreignKey} = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$this->$localKey]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($results as $result) {
            $relatedObject = new $relatedClass($this->pdo);
            $relatedObject->fillFromDatabase($result);

            $pivotData = [];
            foreach ($result as $key => $value) {
                if (strpos($key, $pivotTable . '_') === 0) {
                    $pivotData[substr($key, strlen($pivotTable) + 1)] = $value;
                }
            }

            if (method_exists($relatedObject, 'setPivot')) {
                $relatedObject->setPivot(new PivotContainer($pivotData));
            }

            $objects[] = $relatedObject;
        }

        return $objects;
    }

    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }

        foreach ($relations as $relation) {
            if (method_exists($this, $relation)) {
                $relatedData = $this->$relation();
                $property = strtolower($relation);  // Nome da relação como propriedade
                $this->$property = $relatedData;
            }
        }
        return $this;
    }

    /**
     * Retorna todos os registros da tabela.
     */
    public function all()
    {
        $stmt = $this->pdo->query("SELECT * FROM {$this->tableName}");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($results as $result) {
            $object = new static();
            $object->fillFromDatabase($result);
            $objects[] = $object;
        }

        return $objects;
    }

    /**
     * Encontra um registro pelo seu ID.
     */
    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $object = new static();
            $object->fillFromDatabase($result);
            return $object;
        }

        return null;
    }

    /**
     * Cria um novo registro no banco de dados.
     */
    public function create(array $data)
    {
        if (!$this->tableName) {
            throw new \Exception("No table specified for the operation.");
        }

        // Sempre inclua o primaryKey no fillable
        $this->fillable[] = $this->primaryKey;
        $this->fillable = array_unique($this->fillable);

        // Atualizar $fillable com as chaves de $data, caso ainda não estejam
        $this->fillable = array_unique(array_merge($this->fillable, array_keys($data)));

        // Filtrar apenas as colunas que são preenchíveis
        $filteredData = array_intersect_key($data, array_flip($this->fillable));

        // Criar string de colunas e placeholders para a consulta SQL
        $columns = implode(', ', array_keys($filteredData));
        $placeholders = ':' . implode(', :', array_keys($filteredData));

        // Preparar a instrução SQL
        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);

        // Executar a instrução SQL
        $success = $stmt->execute($filteredData);

        if ($success) {
            // Obter o último ID inserido
            $lastInsertId = $this->pdo->lastInsertId();

            // Se o primaryKey não for 'id', não podemos garantir que lastInsertId seja correto
            if ($this->primaryKey == 'id') {
                $stmt = $this->pdo->prepare("SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id");
                $stmt->execute(['id' => $lastInsertId]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $object = new static();
                    $object->fillFromDatabase($result);
                    return $object;
                }
            } else {
                // Para outras chaves primárias, apenas preencha o objeto com os dados inseridos
                $object = new static();
                $object->fill($filteredData);
                return $object;
            }
        }

        // Retornar false se a inserção falhar
        return false;
    }

    /**
     * Atualiza um registro existente.
     */
    public function update($id, array $data)
    {
        $filteredData = array_intersect_key($data, array_flip($this->fillable));

        $fields = '';
        foreach ($filteredData as $column => $value) {
            $fields .= "{$column} = :{$column}, ";
        }
        $fields = rtrim($fields, ', ');

        $sql = "UPDATE {$this->tableName} SET {$fields} WHERE id = :id";
        $filteredData['id'] = $id;  // Adiciona o id à lista de parâmetros
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($filteredData);
    }

    /**
     * Deleta um registro do banco de dados.
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    private function validateOperator($operator)
    {
        $validOperators = ['=', '>=', '>', '<', '<=', 'LIKE'];
        if (!in_array($operator, $validOperators, true)) {
            throw new InvalidArgumentException("Operador inválido: $operator");
        }
    }

    public function __set($key, $value)
    {
        if (in_array($key, $this->fillable) || $key === $this->primaryKey) {
            $this->attributes[$key] = $value;
        } else {
            throw new \Exception("The attribute '{$key}' is not fillable.");
        }
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    protected function fillFromDatabase(array $dataArray)
    {
        foreach ($dataArray as $key => $value) {
            if (in_array($key, $this->fillable) || $key === $this->primaryKey) {
                $this->$key = $value;
            } else {
                $this->attributes[$key] = $value;
            }
        }
    }

    public function resetQuery()
    {
        $this->selectColumns = '*';
        $this->query = "SELECT {$this->selectColumns} FROM {$this->tableName}";
        $this->bindings = [];
    }
}
