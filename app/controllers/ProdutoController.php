<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Produto;

class ProdutoController extends Controller
{
    public function index(): void
    {
        $produtos = Produto::all();
        $this->view('produtos/index', ['produtos' => $produtos]);
    }

    public function create(): void
    {
        $this->view('produtos/create');
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $data = [
            'nome' => $_POST['nome'] ?? '',
            'preco' => (float)($_POST['preco'] ?? 0),
            'descricao' => $_POST['descricao'] ?? ''
        ];

        if (empty($data['nome']) || $data['preco'] <= 0) {
            $_SESSION['error'] = 'Nome e preço são obrigatórios.';
            header('Location: /produto/new');
            exit;
        }

        $produtoId = Produto::create($data);
        
        // Criar estoque inicial se informado
        if (!empty($_POST['estoque_inicial'])) {
            $estoqueInicial = (int)$_POST['estoque_inicial'];
            Produto::updateEstoque($produtoId, null, $estoqueInicial);
        }

        $_SESSION['success'] = 'Produto criado com sucesso!';
        header('Location: /produto/' . $produtoId);
        exit;
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $produto = Produto::find($id);
        
        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: /');
            exit;
        }

        $variacoes = Produto::getVariacoes($id);
        $estoque = Produto::getEstoque($id);
        
        $this->view('produtos/show', [
            'produto' => $produto,
            'variacoes' => $variacoes,
            'estoque' => $estoque
        ]);
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $produto = Produto::find($id);
        
        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: /');
            exit;
        }

        $estoque = Produto::getEstoque($id);
        
        $this->view('produtos/edit', [
            'produto' => $produto,
            'estoque' => $estoque
        ]);
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $produto = Produto::find($id);
        
        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: /');
            exit;
        }

        $data = [
            'nome' => $_POST['nome'] ?? '',
            'preco' => (float)($_POST['preco'] ?? 0),
            'descricao' => $_POST['descricao'] ?? ''
        ];

        if (empty($data['nome']) || $data['preco'] <= 0) {
            $_SESSION['error'] = 'Nome e preço são obrigatórios.';
            header('Location: /produto/' . $id . '/edit');
            exit;
        }

        Produto::update($id, $data);
        
        // Atualizar estoque se informado
        if (isset($_POST['estoque'])) {
            $estoque = (int)$_POST['estoque'];
            Produto::updateEstoque($id, null, $estoque);
        }

        $_SESSION['success'] = 'Produto atualizado com sucesso!';
        header('Location: /produto/' . $id);
        exit;
    }

    public function addToCart(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $produtoId = (int)($_POST['produto_id'] ?? 0);
        $variacaoId = !empty($_POST['variacao_id']) ? (int)$_POST['variacao_id'] : null;
        $quantidade = (int)($_POST['quantidade'] ?? 1);

        $produto = Produto::find($produtoId);
        if (!$produto) {
            $_SESSION['error'] = 'Produto não encontrado.';
            header('Location: /');
            exit;
        }

        // Verificar estoque disponível
        $estoqueDisponivel = Produto::getEstoque($produtoId, $variacaoId);
        if ($quantidade > $estoqueDisponivel) {
            $_SESSION['error'] = 'Estoque insuficiente. Disponível: ' . $estoqueDisponivel;
            header('Location: /produto/' . $produtoId);
            exit;
        }

        // Inicializar carrinho se não existir
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        // Chave única para o item (produto + variação)
        $chaveItem = $produtoId . '_' . ($variacaoId ?? '0');

        // Adicionar ao carrinho
        if (isset($_SESSION['carrinho'][$chaveItem])) {
            $_SESSION['carrinho'][$chaveItem]['quantidade'] += $quantidade;
        } else {
            $_SESSION['carrinho'][$chaveItem] = [
                'produto_id' => $produtoId,
                'variacao_id' => $variacaoId,
                'nome' => $produto['nome'],
                'preco' => $produto['preco'],
                'quantidade' => $quantidade
            ];
        }

        $_SESSION['success'] = 'Produto adicionado ao carrinho!';
        header('Location: /produto/' . $produtoId);
        exit;
    }
}
