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

        $variacoes = Produto::getVariacoes($id);
        $estoque = Produto::getEstoque($id);
        
        $this->view('produtos/edit', [
            'produto' => $produto,
            'variacoes' => $variacoes,
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
        
        // Verificar variações atuais do produto
        $variacoesExistentes = Produto::getVariacoes($id);
        $temVariacoesExistentes = !empty($variacoesExistentes);
        
        // Verificar se existem variações sendo enviadas
        $temVariacoes = isset($_POST['variacoes']) && is_array($_POST['variacoes']) && !empty($_POST['variacoes']);
        $temVariacoesValidas = false;
        
        if ($temVariacoes) {
            // Verificar se há pelo menos uma variação válida
            foreach ($_POST['variacoes'] as $variacaoData) {
                $tipo = trim($variacaoData['tipo'] ?? '');
                $valor = trim($variacaoData['valor'] ?? '');
                if (!empty($tipo) && !empty($valor)) {
                    $temVariacoesValidas = true;
                    break;
                }
            }
        }
        
        if ($temVariacoesValidas) {
            // MODO VARIAÇÕES: Processar variações
            // Se produto não tinha variações antes, remove estoque geral
            if (!$temVariacoesExistentes) {
                Produto::removeEstoqueGeral($id);
            }
            
            // Processar variações
            foreach ($_POST['variacoes'] as $variacaoData) {
                $variacaoId = !empty($variacaoData['id']) ? (int)$variacaoData['id'] : null;
                $tipo = trim($variacaoData['tipo'] ?? '');
                $valor = trim($variacaoData['valor'] ?? '');
                $estoque = (int)($variacaoData['estoque'] ?? 0);
                
                // Pular variações vazias
                if (empty($tipo) || empty($valor)) {
                    continue;
                }
                
                if ($variacaoId) {
                    // Atualizar variação existente
                    Produto::updateVariacao($variacaoId, $tipo, $valor);
                    Produto::updateEstoque($id, $variacaoId, $estoque);
                } else {
                    // Criar nova variação
                    $novaVariacaoId = Produto::createVariacao($id, $tipo, $valor);
                    if ($estoque > 0) {
                        Produto::updateEstoque($id, $novaVariacaoId, $estoque);
                    }
                }
            }
        } else {
            // MODO ESTOQUE GERAL: Atualizar estoque geral
            // Só remove variações se o usuário explicitamente removeu todas
            
            // Atualizar estoque geral se informado
            if (isset($_POST['estoque'])) {
                $estoque = (int)$_POST['estoque'];
                Produto::updateEstoque($id, null, $estoque);
            }
        }
        
        // Remover variações marcadas para exclusão
        if (isset($_POST['remover_variacoes']) && is_array($_POST['remover_variacoes'])) {
            foreach ($_POST['remover_variacoes'] as $variacaoId) {
                Produto::removeVariacao((int)$variacaoId);
            }
        }
        
        // Verificar se todas as variações foram removidas após processamento
        $variacoesRestantes = Produto::getVariacoes($id);
        if ($temVariacoesExistentes && empty($variacoesRestantes) && !$temVariacoesValidas) {
            // Se tinha variações antes, não tem mais agora, e não está adicionando novas
            // então o usuário removeu todas - voltar ao modo estoque geral
            // (O estoque geral já foi atualizado acima se necessário)
        }

        $_SESSION['success'] = 'Produto atualizado com sucesso!';
        header('Location: /produto/show?id=' . $id);
        exit;
    }

    public function delete(): void
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

        if (Produto::delete($id)) {
            $_SESSION['success'] = 'Produto excluído com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao excluir produto.';
        }
        
        header('Location: /');
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
