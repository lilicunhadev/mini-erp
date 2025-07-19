<?php
$title = 'Editar ' . htmlspecialchars($produto['nome']) . ' - Mini ERP';
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Produto</h4>
            </div>
            <div class="card-body">
                <form action="/produto/update" method="POST">
                    <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Produto *</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?= htmlspecialchars($produto['nome']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="preco" class="form-label">Preço *</label>
                        <div class="input-group">
                            <span class="input-group-text">R$</span>
                            <input type="number" class="form-control" id="preco" name="preco" 
                                   step="0.01" min="0.01" value="<?= $produto['preco'] ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="estoque" class="form-label">Estoque Atual</label>
                        <input type="number" class="form-control" id="estoque" name="estoque" 
                               min="0" value="<?= $estoque ?>">
                        <div class="form-text">Quantidade atual em estoque</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                        <a href="/produto/show?id=<?= $produto['id'] ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
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
