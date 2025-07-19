<?php
$title = 'Novo Produto - Mini ERP';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Novo Produto</h4>
            </div>
            <div class="card-body">
                <form action="/produto/store" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>

                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="preco" name="preco" 
                                   step="0.01" min="0.01" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="estoque_inicial" class="form-label">Estoque Inicial</label>
                        <input type="number" class="form-control" id="estoque_inicial" name="estoque_inicial" 
                               min="0" value="0">
                        <div class="form-text">Quantidade inicial em estoque (opcional)</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Produto
                        </button>
                        <a href="/" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/views/layout.php';
?>
