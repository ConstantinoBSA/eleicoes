Métodos Básicos
CRUD Operations:

all(): Retorna todos os registros da tabela.
find($id): Encontra um registro pelo seu ID.
create(array $attributes): Cria um novo registro no banco de dados.
update(array $attributes): Atualiza um registro existente.
delete(): Deleta um registro do banco de dados.
Query Methods:

where($column, $operator = null, $value = null, $boolean = 'and'): Adiciona uma cláusula where à consulta.
orWhere(...): Adiciona uma cláusula orWhere.
first(): Retorna o primeiro registro que atende aos critérios da consulta.
get(): Retorna os resultados da consulta como uma coleção.
count(): Retorna o número de registros que atendem aos critérios especificados.
Aggregate Functions:

sum($column): Retorna a soma dos valores de uma coluna.
avg($column): Retorna a média dos valores de uma coluna.
min($column): Retorna o valor mínimo de uma coluna.
max($column): Retorna o valor máximo de uma coluna.
Relationships:

hasOne(): Define uma relação um-para-um.
hasMany(): Define uma relação um-para-muitos.
belongsTo(): Define uma relação de pertencimento.
belongsToMany(): Define uma relação muitos-para-muitos.
with(): Carrega relações junto com os resultados da consulta.
Funcionalidades Avançadas
Mass Assignment:

$fillable: Define quais atributos podem ser atribuídos em massa.
$guarded: Define quais atributos estão protegidos contra atribuição em massa.
Accessors and Mutators:

Getters e setters personalizados para manipular atributos enquanto os acessa ou modifica.
Scopes:

Métodos que definem condições comuns para reutilizar em consultas (scopeActive, scopePopular, etc.).
Soft Deletes:

use SoftDeletes: Permite “deletar” registros sem realmente removê-los do banco de dados.
trashed(): Verifica se um registro foi "soft deleted".
withTrashed(): Inclui registros "soft deleted" nos resultados de consulta.
Event Handling:

boot(): Método estático usado para inicializar eventos do modelo, como creating, updating, deleting, etc.
Attribute Casting:

$casts: Define como os atributos devem ser convertidos quando acessados.
Timestamp Management:

timestamps: Habilita/desabilita automaticamente created_at e updated_at.

docker ps -a

docker stop b41b85c8ead7
docker rm b41b85c8ead7
