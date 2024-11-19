<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\PDF;
use App\Models\Venda;

class RelatorioController extends Controller
{
    public function vendas()
    {
        $this->view('admin/relatorios/vendas');
    }

    public function impressao()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataInicial = $_POST['data_inicial'];
            $dataFinal = $_POST['data_final'];

            // Sanitizar e validar as datas
            $dataInicial = date('Y-m-d', strtotime($dataInicial));
            $dataFinal = date('Y-m-d', strtotime($dataFinal));

            // Chamar o modelo para buscar vendas no intervalo especificado
            $vendaModel = new Venda();
            $vendas = $vendaModel->obterVendasPorPeriodo($dataInicial, $dataFinal);

            // Buffer para capturar o output da view
            ob_start();
            include '../resources/views/admin/relatorios/impressos/vendas.php';
            $htmlContent = ob_get_clean();

            // Crie uma nova instância do CustomPDF
            $pdf = new PDF();

            // Adicione uma nova página
            $pdf->AddPage();

            // Adicione o conteúdo HTML ao PDF
            $pdf->writeHTML($htmlContent, true, false, true, false, '');

            // Envie o PDF para o navegador
            $pdf->Output('relatorios-vendas.pdf', 'I');
        } else {
            // Redirecionar ou mostrar um erro
            header('Location: /admin/vendas/relatorio');
            exit();
        }
    }
}
