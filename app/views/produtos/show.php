<?php
$title = htmlspecialchars($produto['nome']) . ' - Mini ERP';
ob_start();
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?= htmlspecialchars($produto['nome']) ?></h4>
                <div class="btn-group">
                    <a href="/produto/edit?id=<?= $produto['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="/" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-success">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></h5>
                        <?php if (!empty($produto['descricao'])): ?>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <div class="alert <?= $estoque > 0 ? 'alert-success' : 'alert-warning' ?>">
                            <strong>Estoque:</strong> <?= $estoque ?> unidades
                        </div>
                    </div>
                </div>

                <?php if (!empty($variacoes)): ?>
                    <hr>
                    <h6>Variações Disponíveis:</h6>
                    <div class="row">
                        <?php foreach ($variacoes as $variacao): ?>
                            <div class="col-md-4 mb-2">
                                <div class="card">
                                    <div class="card-body p-2">
                                        <small class="fw-bold"><?= htmlspecialchars($variacao['nome']) ?></small>
                                        <br>
                                        <small class="text-muted">Estoque: <?= $variacao['estoque'] ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cart-plus"></i> Comprar</h5>
            </div>
            <div class="card-body">
                <?php if ($estoque > 0 || !empty($variacoes)): ?>
                    <form action="/produto/add-to-cart" method="POST">
                        <input type="hidden" name="produto_id" value="<?= $produto['id'] ?>">
                        
                        <?php if (!empty($variacoes)): ?>
                            <div class="mb-3">
                                <label for="variacao_id" class="form-label">Variação</label>
                                <select class="form-select" name="variacao_id" id="variacao_id">
                                    <option value="">Produto base</option>
                                    <?php foreach ($variacoes as $variacao): ?>
                                        <?php if ($variacao['estoque'] > 0): ?>
                                            <option value="<?= $variacao['id'] ?>">
                                                <?= htmlspecialchars($variacao['nome']) ?> 
                                                (<?= $variacao['estoque'] ?> disponível)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="quantidade" class="form-label">Quantidade</label>
                            <input type="number" class="form-control" name="quantidade" id="quantidade" 
                                   value="1" min="1" max="<?= $estoque ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Produto sem estoque disponível.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/views/layout.php';
?>
