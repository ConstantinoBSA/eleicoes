<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Configuracao;

class HomeController extends Controller
{
    protected $configModel;

    public function __construct()
    {
        $this->configModel = new Configuracao();
    }

    public function index()
    {
        $this->view('admin/home', [
            //
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
