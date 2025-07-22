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

                    <!-- Campo de Estoque Geral -->
                    <div class="mb-3">
                        <label for="estoque" class="form-label">Estoque Geral</label>
                        <input type="number" class="form-control" id="estoque" name="estoque" 
                               min="0" value="<?= $estoque ?>">
                        <div class="form-text">
                            <?php if (empty($variacoes)): ?>
                                Quantidade total em estoque (produto sem variações)
                            <?php else: ?>
                                <strong>Atenção:</strong> Este produto tem variações. O estoque é controlado individualmente por variação abaixo. 
                                Este campo só deve ser usado se você quiser remover todas as variações e voltar ao estoque simples.
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Seção de Variações -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0"><i class="bi bi-palette"></i> Variações do Produto</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="adicionarVariacao()">
                                <i class="bi bi-plus-circle"></i> Adicionar Variação
                            </button>
                        </div>
                        
                        <div id="variacoes-container">
                            <?php if (!empty($variacoes)): ?>
                                <?php foreach ($variacoes as $index => $variacao): ?>
                                <div class="variacao-item border rounded p-3 mb-3" data-index="<?= $index ?>">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Tipo</label>
                                            <input type="text" class="form-control" 
                                                   name="variacoes[<?= $index ?>][tipo]" 
                                                   value="<?= htmlspecialchars($variacao['nome']) ?>" 
                                                   placeholder="Ex: Tamanho, Cor">
                                            <input type="hidden" name="variacoes[<?= $index ?>][id]" value="<?= $variacao['id'] ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Valor</label>
                                            <input type="text" class="form-control" 
                                                   name="variacoes[<?= $index ?>][valor]" 
                                                   value="<?= htmlspecialchars($variacao['valor']) ?>" 
                                                   placeholder="Ex: M, Azul">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Estoque</label>
                                            <input type="number" class="form-control" 
                                                   name="variacoes[<?= $index ?>][estoque]" 
                                                   value="<?= $variacao['estoque'] ?>" 
                                                   min="0">
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="removerVariacao(this, <?= $variacao['id'] ?>)">
                                                <i class="bi bi-trash"></i> Remover
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-muted text-center py-3">
                                    <i class="bi bi-info-circle"></i> Nenhuma variação cadastrada. 
                                    Clique em "Adicionar Variação" para criar uma.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Container para variações marcadas para remoção -->
                        <div id="variacoes-remover"></div>
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

<script>
let variacaoIndex = <?= count($variacoes) ?>;

function adicionarVariacao() {
    const container = document.getElementById('variacoes-container');
    const novaVariacao = document.createElement('div');
    novaVariacao.className = 'variacao-item border rounded p-3 mb-3';
    novaVariacao.setAttribute('data-index', variacaoIndex);
    
    novaVariacao.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <input type="text" class="form-control" 
                       name="variacoes[${variacaoIndex}][tipo]" 
                       placeholder="Ex: Tamanho, Cor">
            </div>
            <div class="col-md-3">
                <label class="form-label">Valor</label>
                <input type="text" class="form-control" 
                       name="variacoes[${variacaoIndex}][valor]" 
                       placeholder="Ex: M, Azul">
            </div>
            <div class="col-md-3">
                <label class="form-label">Estoque</label>
                <input type="number" class="form-control" 
                       name="variacoes[${variacaoIndex}][estoque]" 
                       value="0" min="0">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger btn-sm" 
                        onclick="removerVariacao(this)">
                    <i class="bi bi-trash"></i> Remover
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(novaVariacao);
    variacaoIndex++;
}

function removerVariacao(button, variacaoId = null) {
    const variacaoItem = button.closest('.variacao-item');
    
    if (variacaoId) {
        // Variação existente - marcar para remoção
        const removerContainer = document.getElementById('variacoes-remover');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remover_variacoes[]';
        hiddenInput.value = variacaoId;
        removerContainer.appendChild(hiddenInput);
    }
    
    variacaoItem.remove();
}
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/app/views/layout.php';
?>
