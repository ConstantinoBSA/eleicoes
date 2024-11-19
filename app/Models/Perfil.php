<?php

namespace App\Models;

use App\Core\Model;

class Perfil extends Model
{
    protected $tableName = 'perfis';
    protected $fillable = ['id', 'nome', 'label', 'descricao'];

    public function permissoes()
    {
        return $this->hasMany(PermissaoPerfil::class, 'perfil_id', 'id');
    }
}
