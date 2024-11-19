<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Validator;
use App\Core\Sanitizer;
use App\Core\AuditLogger;
use App\Models\Categoria;
use App\Models\Produto;

class ProdutoController extends Controller
{
    private $produtoModel;
    private $categoriaModel;

    public function __construct()
    {
        parent::__construct();
        $this->produtoModel = new Produto();
        $this->categoriaModel = new Categoria();

        if (!$this->hasPermission('produtos')) {
            abort('403', 'Você não tem acesso a está área do sistema');
        }
    }

    public function index()
    {
        try {
            $perPage = 10;
            $currentPage = $this->request->get('page', 1);
            $search = $this->request->get('search', '');

            $produtos = $this->produtoModel
                ->where('nome', 'LIKE', $search)
                ->orWhere('codigo_barras', $search)
                ->orderBy('nome')
                ->paginate($perPage, $currentPage);

            $this->view('admin/produtos/index', [
                'produtos' => $produtos,
            ]);
        } catch (\Exception $e) {
            $this->handleException($e, 'Ocorreu um erro ao obter produtos.');
        }
    }

    public function create()
    {
        $errors = $_SESSION['errors'] ?? [];
        $oldData = $_SESSION['old_data'] ?? [];

        unset($_SESSION['errors'], $_SESSION['old_data']);

        $categorias = $this->categoriaModel->where('status', true)->get();

        $this->view('admin/produtos/create', [
            'categorias' => $categorias,
            'errors' => $errors,
            'data' => $oldData
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/produtos/create', 'Token CSRF inválido.', 'error');
            }         

            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $nome = $this->sanitizer->sanitizeString($_POST['nome']);
            $slug = generateSlug($nome); // Gerar slug automaticamente
            $codigo = generateProductNumber($nome); // Gerar slug automaticamente
            $preco = (float) str_replace(['.', ','], ['', '.'], $_POST['preco']);
            $preco_promocional = (float) str_replace(['.', ','], ['', '.'], $_POST['preco_promocional']);

            $rules = [
                'nome' => 'required|unique:produtos',
                'descricao' => 'required',
                'preco' => 'required',
                'preco_promocional' => 'required',
                'estoque' => 'required',
                'categoria_id' => 'required',
                'informacoes_relevantes' => 'required',
                'data_lancamento' => 'required',
                'pontos' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/produtos/adicionar');
            } else {
    
                $produto = $this->produtoModel->create([
                    'nome' => $sanitizedData['nome'], 
                    'codigo' => $codigo,
                    'descricao' => $sanitizedData['descricao'],
                    'preco' => $preco,
                    'preco_promocional' => $preco_promocional,
                    'codigo_barras' => $sanitizedData['codigo_barras'],
                    'estoque' => $sanitizedData['estoque'],
                    'slug' => $slug,
                    'imagem' => $sanitizedData['imagem'],
                    'categoria_id' => $sanitizedData['categoria_id'],
                    'informacoes_relevantes' => $sanitizedData['informacoes_relevantes'],
                    'data_lancamento' => $sanitizedData['data_lancamento'],
                    'pontos' => $sanitizedData['pontos'],
                    'promocao' => $sanitizedData['promocao'],
                    'destaque' => $sanitizedData['destaque'],
                    'status' => $sanitizedData['status']
                ]);
                if ($produto) {
                    $_SESSION['message'] = "Registro adicionaado com sucesso!";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = 'Erro ao adicionar produto. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/produtos/index');
            }
        }
    }

    public function edit($id)
    {
        try {
            $produto = $this->produtoModel->find($id);
            if (!$produto) {
                throw new \Exception('Categoria não encontrada.', 404);
            }

            $errors = $_SESSION['errors'] ?? [];
            $oldData = $_SESSION['old_data'] ?? [];

            unset($_SESSION['errors'], $_SESSION['old_data']);

            foreach ($oldData as $key => $value) {
                if (property_exists($produto, $key)) {
                    $produto->$key = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                }
            }

            $categorias = $this->categoriaModel->where('status', 1)->get();

            $this->view('admin/produtos/edit', [
                'categorias' => $categorias,
                'produto' => $produto,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dadosRequisicao = $this->request->all();

            if (!$this->validateCsrfToken($dadosRequisicao['csrf_token'])) {
                $this->redirectToWithMessage('/admin/produtos/editar/' . $id, 'Token CSRF inválido.', 'error');
            }         
            
            $sanitizedData = $this->sanitizeData($dadosRequisicao);

            $nome = $this->sanitizer->sanitizeString($_POST['nome']);
            $slug = generateSlug($nome);
            $preco = (float) str_replace(['.', ','], ['', '.'], $_POST['preco']);
            $preco_promocional = (float) str_replace(['.', ','], ['', '.'], $_POST['preco_promocional']);  

            $rules = [
                'nome' => 'required|unique:produtos,nome,'.$id,
                'descricao' => 'required',
                'preco' => 'required',
                'preco_promocional' => 'required',
                'estoque' => 'required',
                'categoria_id' => 'required',
                'informacoes_relevantes' => 'required',
                'data_lancamento' => 'required',
                'pontos' => 'required',
                'status' => 'required'
            ];

            $errors = $this->validator->validate($sanitizedData, $rules);

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $dadosRequisicao;
                $this->redirect('/admin/produtos/editar/' . $id);
            } else {
                $produto = $this->produtoModel->update($id, [
                    'nome' => $sanitizedData['nome'], 
                    'descricao' => $sanitizedData['descricao'],
                    'preco' => $preco,
                    'preco_promocional' => $preco_promocional,
                    'codigo_barras' => $sanitizedData['codigo_barras'],
                    'estoque' => $sanitizedData['estoque'],
                    'slug' => $slug,
                    'imagem' => $sanitizedData['imagem'],
                    'categoria_id' => $sanitizedData['categoria_id'],
                    'informacoes_relevantes' => $sanitizedData['informacoes_relevantes'],
                    'data_lancamento' => $sanitizedData['data_lancamento'],
                    'pontos' => $sanitizedData['pontos'],
                    'promocao' => $sanitizedData['promocao'],
                    'destaque' => $sanitizedData['destaque'],
                    'status' => $sanitizedData['status']
                ]);

                if ($produto) {
                    $_SESSION['message'] = "Registro editado com sucesso!";
                    $_SESSION['message_type'] = "success";

                    $this->auditLogger->log(auth()->user()->id, 'Descrição da ação', 'Detalhes adicionais sobre a ação');
                } else {
                    $_SESSION['message'] = 'Erro ao editar produto. Por favor, tente novamente!';
                    $_SESSION['message_type'] = "success";
                }

                // Token válido, remova-o da sessão
                unset($_SESSION['csrf_token']);

                header('Location: /admin/produtos/index');
            }
        }
    }

    public function show($id)
    {
        try {
            $produto = $this->produtoModel->find($id);
            if ($produto) {
                $this->view('admin/produtos/show', ['produto' => $produto]);
            } else {
                $this->renderErrorPage('Erro 404', 'Produto não encontrado.');
            }
        } catch (\Exception $e) {
            $this->renderErrorPage('Erro 404', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $produto = $this->produtoModel->delete($id);
        if ($produto) {
            $_SESSION['message'] = "Registro deletado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = 'Erro ao editar deletar. Por favor, tente novamente!';
            $_SESSION['message_type'] = "success";
        }
        header('Location: /admin/produtos/index');
    }

    // Função para renderizar a view de erro
    private function renderErrorPage($title, $message)
    {
        $errorTitle = $title;
        $errorMessage = $message;
        include '../resources/views/error.php';
        exit();
    }

    private function sanitizeData($data)
    {
        return [
            'nome' => $this->sanitizer->sanitizeString($_POST['nome']),
            'descricao' => $this->sanitizer->sanitizeString($_POST['descricao']),
            'codigo_barras' => $this->sanitizer->sanitizeString($_POST['codigo_barras']),
            'estoque' => $this->sanitizer->sanitizeString($_POST['estoque']),
            'preco' => $this->sanitizer->sanitizeString($_POST['preco']),
            'preco_promocional' => $this->sanitizer->sanitizeString($_POST['preco_promocional']),
            'imagem' => null,
            'categoria_id' => $this->sanitizer->sanitizeString($_POST['categoria_id']),
            'informacoes_relevantes' => $this->sanitizer->sanitizeString($_POST['informacoes_relevantes']),
            'data_lancamento' => $this->sanitizer->sanitizeString($_POST['data_lancamento']),
            'pontos' => $this->sanitizer->sanitizeString($_POST['pontos']),
            'promocao' => $this->sanitizer->sanitizeString($_POST['promocao']),
            'destaque' => $this->sanitizer->sanitizeString($_POST['destaque']),
            'status' => $this->sanitizer->sanitizeString($_POST['status']),
        ];
    }
}
