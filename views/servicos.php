<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Busca todos os serviços cadastrados organizados por categoria
$sql = "SELECT * FROM servicos ORDER BY categoria DESC, nome_servico ASC";
$servicos = $pdo->query($sql)->fetchAll();
?>

<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-secondary mb-1">Catálogo de Serviços e Preços</h2>
            <p class="text-muted small">Tabela de procedimentos clínicos e estéticos oferecidos.</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalServico">
            <i class="fa-solid fa-plus me-1"></i> Novo Serviço
        </button>
    </div>

    <div class="card border-0 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome do Serviço / Procedimento</th>
                        <th>Categoria Oficial</th>
                        <th>Preço Base de Venda</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($servicos)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Nenhum serviço mapeado no catálogo ainda.</td>
                        </tr>
                    <?php else: foreach($servicos as $servico): 
                        $cat_badge = $servico['categoria'] === 'clinica' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary';
                        $cat_nome  = $servico['categoria'] === 'clinica' ? 'Clínico / Veterinário' : 'Estético / Banho e Tosa';
                    ?>
                        <tr>
                            <td>#<?= $servico['id'] ?></td>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($servico['nome_servico']) ?></td>
                            <td>
                                <span class="badge <?= $cat_badge ?> px-3 py-2 text-uppercase font-monospace" style="font-size: 0.75rem;">
                                    <?= $cat_nome ?>
                                </span>
                            </td>
                            <td class="fw-bold text-secondary">R$ <?= number_format($servico['preco'], 2, ',', '.') ?></td>
                            <td class="text-center">
                                <a href="../actions/servico-action.php?acao=deletar&id=<?= $servico['id'] ?>" 
                                   class="text-muted text-danger-hover text-decoration-none small" 
                                   onclick="return confirm('Deseja retirar este serviço do portfólio?')">
                                    <i class="fa-solid fa-trash-can"></i> Remover
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalServico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Mapear Novo Serviço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal"></button>
            </div>
            <form action="../actions/servico-action.php?acao=cadastrar" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Procedimento</label>
                        <input type="text" class="form-control" name="nome_servico" placeholder="Ex: Consulta Ortopédica, Tosa Bebê" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoria</label>
                            <select class="form-select" name="categoria" required>
                                <option value="estetica" selected>Estética (Banho/Tosa)</option>
                                <option value="clinica">Clínica (Médico/Vet)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preço de Venda (R$)</label>
                            <input type="number" step="0.01" class="form-control" name="preco" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar ao Catálogo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>