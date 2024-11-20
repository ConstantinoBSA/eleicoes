<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\PDF;
use App\Models\Cedula;
use App\Models\Escola;
use App\Models\Venda;

class ImpressaoController extends Controller
{
    public function cadernos()
    {
        $this->view('admin/impressoes/cadernos');
    }

    public function cedulas()
    {
        $escolaModel = new Escola();
        $escolas = $escolaModel->get();

        $this->view('admin/impressoes/cedulas', [
            'escolas' => $escolas
        ]);
    }

    public function impressao()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $escola_id = $_POST['escola_id'];
            $tipo = $_POST['tipo'];

            // Chamar o modelo para buscar vendas no intervalo especificado
            $cedulaModel = new Cedula();
            $cedulas = $cedulaModel
                ->where('escola_id', $escola_id)
                ->where('tipo', $tipo)
                ->get();

            // Buffer para capturar o output da view
            ob_start();
            include './resources/views/admin/impressoes/impressos/cedulas.php';
            $htmlContent = ob_get_clean();

            // Crie uma nova instância do CustomPDF
            $pdf = new PDF();

            // Adicione uma nova página
            $pdf->AddPage();

            // Adicione o conteúdo HTML ao PDF
            $pdf->writeHTML($htmlContent, true, false, true, false, '');

            // Envie o PDF para o navegador
            $pdf->Output('impressoes-cedulas.pdf', 'I');
        } else {
            // Redirecionar ou mostrar um erro
            header('Location: /admin/impressoes/cedulas');
            exit();
        }
    }
}
