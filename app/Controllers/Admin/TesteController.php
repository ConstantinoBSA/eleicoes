<?php

namespace App\Controllers\Admin;

use App\Controllers\Container;
use App\Core\Controller;
use App\Models\Categoria;
use App\Models\PermissaoPerfil;

class TesteController extends Controller
{
    private $pfModel;

    public function __construct()
    {
        parent::__construct();
        $this->pfModel = new PermissaoPerfil();
    }

    public function index()
    {
        
    }
}
