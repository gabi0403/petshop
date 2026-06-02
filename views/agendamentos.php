<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Busca os agendamentos ativos na esteira
$sql = "SELECT a.id, a.data_hora, a.status, p.nome as pet_nome, p.especie, s.nome_servico, u.nome as func_nome
        FROM agendamentos a
        JOIN pets p ON a.pet_id = p.id
        JOIN servicos s ON a.servico_id = s.id
        LEFT JOIN usuarios u ON a.funcionario_id = u.id
        ORDER BY a.data_hora DESC";
$agendamentos = $pdo->query($sql)->fetchAll();

// Listas auxiliares para carregar o formulário de marcação
$pets_list  = $pdo->query("SELECT id, nome, especie FROM pets ORDER BY nome ASC")->fetchAll();
$serv_list  = $pdo->query("SELECT id, nome_servico, preco FROM servicos ORDER BY nome_servico ASC")->fetchAll();
$func_list  = $pdo->query("SELECT id, nome, cargo FROM usuarios WHERE tipo = 'equipe' ORDER BY nome ASC")->fetchAll();
?>

<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-secondary">Central de Agendamentos e Esteira Operacional</h2>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalAgenda">
            <i class="fa-solid fa-calendar-plus me-1"></i> Agendar Serviço
        </button>
    </div>

    <div class="card border-0 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Data / Hora</th>
                        <th>Paciente (Pet)</th>
                        <th>Procedimento</th>
                        <th>Profissional Responsável</th>
                        <th>Status Atual</th>
                        <th class="text-center">Atualizar Esteira</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($agendamentos)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhum atendimento na esteira para os próximos períodos.</td>
                        </tr>
                    <?php else: foreach($agendamentos as $agenda): 
                        // Cores dinâmicas para o status da esteira
                        $status_class = 'bg-secondary';
                        if ($agenda['status'] === 'Em Atendimento') $status_class = 'bg-warning text-dark';
                        if ($agenda['status'] === 'Pronto para Retirada') $status_class = 'bg-info text-dark';
                        if ($agenda['status'] === 'Finalizado') $status_class = 'bg-success';
                    ?>
                        <tr>
                            <td class="fw-semibold"><?= date('d/m/Y H:i', strtotime($agenda['data_hora'])) ?></td>
                            <td>
                                <strong class="text-dark"><?= $agenda['pet_nome'] ?></strong> 
                                <span class="text-muted small">(<?= $agenda['especie'] ?>)</span>
                            </td>
                            <td><?= $agenda['nome_servico'] ?></td>
                            <td><i class="fa-solid fa-user-doctor text-muted me-1"></i> <?= $agenda['func_nome'] ?: 'Não definido' ?></td>
                            <td><span class="badge <?= $status_class ?>"><?= $agenda['status'] ?></span></td>
                            <td class="text-center">
                                <form action="../actions/agenda-action.php?acao=alterar_status" method="POST" class="d-flex justify-content-center g-1">
                                    <input type="hidden" name="agenda_id" value="<?= $agenda['id'] ?>">
                                    <select class="form-select form-select-sm me-1" name="novo_status" style="width: 160px;">
                                        <option value="Agendado" <?= $agenda['status'] == 'Agendado' ? 'selected' : '' ?>>Agendado</option>
                                        <option value="Em Atendimento" <?= $agenda['status'] == 'Em Atendimento' ? 'selected' : '' ?>>Em Atendimento</option>
                                        <option value="Pronto para Retirada" <?= $agenda['status'] == 'Pronto para Retirada' ? 'selected' : '' ?>>Pronto para Retirada</option>
                                        <option value="Finalizado" <?= $agenda['status'] == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-dark"><i class="fa-solid fa-rotate"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAgenda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Novo Agendamento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal"></button>
            </div>
            <form action="../actions/agenda-action.php?acao=agendar" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Paciente (Animal)</label>
                        <select class="form-select" name="pet_id" required>
                            <option value="">Escolha o pet...</option>
                            <?php foreach($pets_list as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= $p['nome'] ?> (<?= $p['especie'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Procedimento / Serviço</label>
                        <select class="form-select" name="servico_id" required>
                            <option value="">Escolha o serviço...</option>
                            <?php foreach($serv_list as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['nome_servico'] ?> - R$ <?= number_format($s['preco'], 2, ',', '.') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alocação de Profissional</label>
                        <select class="form-select" name="funcionario_id" required>
                            <option value="">Escolha o responsável...</option>
                            <?php foreach($func_list as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= $f['nome'] ?> (<?= $f['cargo'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Data e Horário</label>
                        <input type="datetime-local" class="form-control" name="data_hora" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Encaixe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>