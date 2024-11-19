<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Categoria;
use App\Models\Configuracao;
use App\Models\Dashboard;
use App\Models\Kit;
use App\Models\Produto;
use App\Models\Venda;

class HomeController extends Controller
{
    protected $configModel;

    public function __construct()
    {
        $this->configModel = new Configuracao();
    }

    public function index()
    {
        $venda = new Venda();
        $total_vendas = $venda->count();

        $kit = new Kit();
        $total_kits = $kit->count();

        $produto = new Produto();
        $total_produtos = $produto->count();

        $categoria = new Categoria();
        $total_categorias = $categoria->count();        

        // Obter dados para o ano atual
        $anoAtual = date('Y');
        $dashboardModel = new Dashboard();
        $vendasPorMes = $dashboardModel->getVendasPorMes($anoAtual);

        $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $dadosVendas = array_fill(0, 12, 0);

        foreach ($vendasPorMes as $mes => $total) {
            $dadosVendas[$mes - 1] = (float) $total; // Subtrai 1 de $mes porque $meses é indexado de 0 a 11
        }

        $this->view('admin/home', [
            'total_vendas' => $total_vendas,
            'total_kits' => $total_kits,
            'total_produtos' => $total_produtos,
            'total_categorias' => $total_categorias,
            'dadosVendas' => $dadosVendas,
            'anoAtual' => $anoAtual,
            'meses' => $meses,
        ]);
    }

    public function perfil()
    {
        $this->view('admin/perfil');
    }

    public function configuracoes()
    {
        $configuracoes = $this->configModel->getAllConfiguracoes();

        $this->view('admin/configuracoes', [
            'configuracoes' => $configuracoes
        ]);
    }

    public function salvarConfiguracoes()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['config'])) {
            $configs = $_POST['config'];
    
            // Obter todas as chaves atuais no banco de dados
            $configuracoesAtuais = $this->configModel->getAllConfiguracoes();
            $chavesAtuais = array_keys($configuracoesAtuais);
    
            // Processar cada entrada do formulário
            foreach ($configs as $chave => $valor) {
                if (!empty($valor)) {
                    // Se a chave já existe, atualize o valor
                    if (in_array($chave, $chavesAtuais)) {
                        $this->configModel->setConfiguracao($chave, $valor);
                    } else {
                        // Caso contrário, adicione uma nova configuração
                        $this->configModel->setConfiguracao($chave, $valor);
                    }
                }
            }
    
            // Verificar chaves que foram removidas do formulário e devem ser excluídas
            foreach ($chavesAtuais as $chaveAtual) {
                if (!isset($configs[$chaveAtual])) {
                    $this->configModel->deleteConfiguracao($chaveAtual);
                }
            }
        }
    
        // Redirecionar ou mostrar mensagem de sucesso
        header('Location: /admin/configuracoes');
        exit();
    }
}
