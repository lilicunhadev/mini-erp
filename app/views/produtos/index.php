<?php
$title = 'Produtos - Mini ERP';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-box-seam"></i> Produtos</h1>
    <a href="/produto/new" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Produto
    </a>
</div>

<?php if (empty($produtos)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Nenhum produto cadastrado ainda.
        <a href="/produto/new" class="alert-link">Cadastre o primeiro produto</a>.
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($produtos as $produto): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($produto['nome']) ?></h5>
                        <p class="card-text text-muted">
                            <?= htmlspecialchars($produto['descricao']) ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 text-success mb-0">
                                R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                            </span>
                            <span class="badge <?= $produto['estoque_total'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                Estoque: <?= $produto['estoque_total'] ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="btn-group w-100" role="group">
                            <a href="/produto/show?id=<?= $produto['id'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="/produto/edit?id=<?= $produto['id'] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/views/layout.php';
?>
