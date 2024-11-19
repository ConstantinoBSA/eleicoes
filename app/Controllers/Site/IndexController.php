<?php

namespace App\Controllers\Site;

use App\Core\Controller;
use App\Models\Dashboard;

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
        $this->view('site/cedulas', [
            //
        ]);
    }
}
