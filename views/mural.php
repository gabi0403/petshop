<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Busca os avisos recentes cruzando com o nome e cargo de quem postou
$sql = "SELECT m.*, u.nome as autor_nome, u.cargo as autor_cargo 
        FROM mural_avisos m
        LEFT JOIN usuarios u ON m.usuario_id = u.id 
        ORDER BY m.data_publicacao DESC";
$avisos = $pdo->query($sql)->fetchAll();
?>

<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-secondary mb-1">Mural Operacional</h2>
            <p class="text-muted small">Notas técnicas e avisos internos da equipe para o plantão.</p>
        </div>
        <button type="button" class="btn btn-dark rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAviso">
            <i class="fa-solid fa-pen-to-square me-1"></i> Fixar Novo Aviso
        </button>
    </div>

    <div class="row g-3">
        <?php if(empty($avisos)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">Nenhum aviso operacional fixado hoje.</p>
            </div>
        <?php else: foreach($avisos as $aviso): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm bg-white" style="border-left: 5px solid #f1c40f !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($aviso['titulo']) ?></h5>
                            <a href="../actions/mural-action.php?acao=deletar&id=<?= $aviso['id'] ?>" 
                               class="text-muted text-danger-hover ms-2" 
                               onclick="return confirm('Deseja retirar este aviso do mural?')">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </div>
                        <p class="text-secondary small mb-4" style="white-space: pre-line;">
                            <?= htmlspecialchars($aviso['conteudo']) ?>
                        </p>
                        <div class="border-top pt-2 mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="d-block fw-semibold text-dark mb-0"><?= $aviso['autor_nome'] ?></small>
                                    <small class="text-muted style-muted" style="font-size: 0.75rem;"><?= $aviso['autor_cargo'] ?></small>
                                </div>
                                <span class="badge bg-light text-muted fw-normal" style="font-size: 0.75rem;">
                                    <?= date('d/m H:i', strtotime($aviso['data_publicacao'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<div class="modal fade" id="modalAviso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Fixar Aviso no Quadro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal"></button>
            </div>
            <form action="../actions/mural-action.php?acao=cadastrar" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título do Alerta</label>
                        <input type="text" class="form-control" name="titulo" placeholder="ex: Restrição Alimentar / Falta de Insumo" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descrição do Recado</label>
                        <textarea class="form-control" name="conteudo" rows="4" placeholder="Escreva os detalhes técnicos do aviso aqui..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-semibold text-dark">Fixar no Mural</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>