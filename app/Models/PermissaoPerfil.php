<?php

namespace App\Models;

use App\Core\Model;

class PermissaoPerfil extends Model
{
    protected $tableName = 'permissao_perfil';
    protected $fillable = ['perfil_id', 'permissao_id', 'created_at', 'updated_at'];

    public $id;
    public $perfil_id;
    public $permissao_id;
    // public $status;

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'perfil_id', 'id');
    }
}
