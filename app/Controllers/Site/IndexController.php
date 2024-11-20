<?php

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Models\Cedula;
use App\Models\Dashboard;
use App\Models\Resultado;

class IndexController extends Controller
{
    public function index()
    {
        $dashboardModel = new Dashboard();
        $escolas = $dashboardModel->escolas();
        $eleitores = $dashboardModel->eleitores();

        // Calcula o total de eleitores por escola
        $totaisPorEscola = [];
        foreach ($eleitores as $escola => $segmentos) {
            $totaisPorEscola[$escola] = $dashboardModel->totalEleitoresPorEscola($segmentos);
        }

        $this->view('site/index', [
            'escolas' => $escolas,
            'eleitores' => $eleitores,
            'totaisPorEscola' => $totaisPorEscola
        ]);
    }

    public function cedulas()
    {
        $cedulaModel = new Cedula();
        $cedulaModel->gerarEExibirCedulas(1);

        $cedulas = $cedulaModel->mostrarCedulas(1);
        
        $this->view('site/cedulas', [
            'cedulas' => $cedulas['cedulas'],
            'chapas' => $cedulas['chapas'],
        ]);
    }

    public function resultados()
    {
        $resultadoModel = new Resultado();
        $resultados = $resultadoModel->gerarResultados();
        
        $this->view('site/resultados', [
            'resultados' => $resultados
        ]);
    }
}
