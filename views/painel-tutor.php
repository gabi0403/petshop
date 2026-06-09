<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'cliente') {
    header("Location: dashboard.php");
    exit;
}

$tutor_id = $_SESSION['usuario_id'];

// 1. Busca todos os pets pertencentes a este tutor logado
$sql_pets = "SELECT * FROM pets WHERE cliente_id = :tutor_id ORDER BY nome ASC";
$stmt_pets = $pdo->prepare($sql_pets);
$stmt_pets->execute([':tutor_id' => $tutor_id]);
$meus_pets = $stmt_pets->fetchAll();

// 2. CORREÇÃO: Busca TODOS os atendimentos ATIVOS na esteira para este tutor (Sem LIMIT 1)
$sql_linha_tempo = "SELECT a.status, a.data_hora, p.nome as pet_nome, s.nome_servico, u.nome as func_nome
                    FROM agendamentos a
                    JOIN pets p ON a.pet_id = p.id
                    JOIN servicos s ON a.servico_id = s.id
                    LEFT JOIN usuarios u ON a.funcionario_id = u.id
                    WHERE p.cliente_id = :tutor_id AND a.status != 'Finalizado'
                    ORDER BY a.data_hora DESC";
$stmt_linha = $pdo->prepare($sql_linha_tempo);
$stmt_linha->execute([':tutor_id' => $tutor_id]);
$atendimentos_ativos = $stmt_linha->fetchAll(); // Trocado para fetchAll()

// Mapeamento numérico dos status para acender as bolinhas da Timeline
$passos = ['Agendado' => 1, 'Em Atendimento' => 2, 'Pronto para Retirada' => 3];

// 3. Busca o histórico de procedimentos já FINALIZADOS
$sql_historico = "SELECT a.data_hora, p.nome as pet_nome, s.nome_servico, s.preco
                  FROM agendamentos a
                  JOIN pets p ON a.pet_id = p.id
                  JOIN servicos s ON a.servico_id = s.id
                  WHERE p.cliente_id = :tutor_id AND a.status = 'Finalizado'
                  ORDER BY a.data_hora DESC";
$stmt_hist = $pdo->prepare($sql_historico);
$stmt_hist->execute([':tutor_id' => $tutor_id]);
$historico = $stmt_hist->fetchAll();
?>

<div class="py-2">
    <div class="mb-4">
        <h2 class="fw-bold text-secondary mb-1">Espaço do Tutor</h2>
        <p class="text-muted small">Acompanhe a estadia dos seus companheiros em tempo real.</p>
    </div>

    <!-- BLOCO 1: MONITORAMENTO AO VIVO (SUPORTA MÚLTIPLOS PETS SIMULTÂNEOS) -->
    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-satellite-dish me-2 text-danger animate__animated animate__flash animate__infinite"></i> Monitoramento Ao Vivo</h5>
    
    <?php if (empty($atendimentos_ativos)): ?>
        <div class="card border-0 shadow-sm p-4 bg-white text-center mb-4">
            <p class="text-muted mb-0"><i class="fa-solid fa-bell-slash me-2"></i> Nenhum pet seu está em procedimento na esteira operacional neste momento.</p>
        </div>
    <?php else: foreach($atendimentos_ativos as $atendimento): 
        $passo_atual = $passos[$atendimento['status']] ?? 0;
    ?>
        <div class="card border-0 shadow-sm p-4 bg-white mb-3" style="border-left: 5px solid #3498db !important;">
            <div class="alert alert-light border p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span class="small text-muted d-block">Pet em Procedimento:</span>
                    <strong class="text-dark fs-5"><i class="fa-solid fa-paw text-secondary me-1"></i> <?= $atendimento['pet_nome'] ?></strong>
                </div>
                <div>
                    <span class="small text-muted d-block">Serviço sendo executado:</span>
                    <strong class="text-secondary"><?= $atendimento['nome_servico'] ?></strong>
                </div>
                <div>
                    <span class="small text-muted d-block">Responsável:</span>
                    <strong class="text-dark"><i class="fa-solid fa-user-doctor text-muted me-1"></i> <?= $atendimento['func_nome'] ?: 'Equipe Geral' ?></strong>
                </div>
            </div>

            <!-- Trilho Visual do Progresso para ESTE pet -->
            <div class="d-flex justify-content-between align-items-center position-relative my-4 px-5 timeline-horizontal">
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 1 ? 'bg-primary text-white shadow' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px; font-weight: bold;">1</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 1 ? 'fw-bold text-primary' : 'text-muted' ?>">Agendado</span>
                </div>
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 2 ? 'bg-warning text-dark fw-bold shadow' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px; font-weight: bold;">2</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 2 ? 'fw-bold text-warning' : 'text-muted' ?>">Em Atendimento</span>
                </div>
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 3 ? 'bg-success text-white fw-bold shadow' : 'bg-light text-muted border' ?>" style="width: 45px; height: 45px; font-weight: bold;">3</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 3 ? 'fw-bold text-success' : 'text-muted' ?>">Pronto para Retirada 🏁</span>
                </div>
            </div>
        </div>
    <?php endforeach; endif; ?>

    <div class="row mt-4">
        <!-- COLUNA DA ESQUERDA: MEUS PETS -->
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-dog me-2"></i>Meus Pets Registrados</h5>
            <div class="row row-cols-1 g-2">
                <?php if(empty($meus_pets)): ?>
                    <div class="col-12">
                        <div class="alert alert-light border text-center">Nenhum animal vinculado ao seu perfil.</div>
                    </div>
                <?php else: foreach($meus_pets as $my_pet): ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm bg-white p-3">
                            <div class="d-flex align-items-center">
                                <img src="../assets/uploads/<?= $my_pet['foto'] ?>" class="rounded-circle border" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='https://placehold.co/60x60?text=Pet'">
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-0 text-dark"><?= $my_pet['nome'] ?></h6>
                                    <small class="text-muted d-block"><?= $my_pet['especie'] ?> • <?= $my_pet['raca'] ?></small>
                                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.7rem;">Humor: <?= $my_pet['comportamento'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <!-- COLUNA DA DIREITA: HISTÓRICO -->
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-clock-rotate-left me-2"></i>Histórico de Visitas Recentes</h5>
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th>Data</th>
                                <th>Pet</th>
                                <th>Serviço</th>
                                <th class="text-end">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if(empty($historico)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Nenhuma ordem de serviço finalizada anteriormente.</td>
                                </tr>
                            <?php else: foreach($historico as $hist): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($hist['data_hora'])) ?></td>
                                    <td class="fw-semibold text-dark"><?= $hist['pet_nome'] ?></td>
                                    <td><?= $hist['nome_servico'] ?></td>
                                    <td class="text-end fw-bold text-secondary"> R$ <?= number_format($hist['preco'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>