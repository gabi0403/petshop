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

// 2. Busca o atendimento mais recente ou ativo na esteira para este tutor
$sql_linha_tempo = "SELECT a.status, a.data_hora, p.nome as pet_nome, s.nome_servico, u.nome as func_nome
                    FROM agendamentos a
                    JOIN pets p ON a.pet_id = p.id
                    JOIN servicos s ON a.servico_id = s.id
                    LEFT JOIN usuarios u ON a.funcionario_id = u.id
                    WHERE p.cliente_id = :tutor_id
                    ORDER BY a.data_hora DESC LIMIT 1";
$stmt_linha = $pdo->prepare($sql_linha_tempo);
$stmt_linha->execute([':tutor_id' => $tutor_id]);
$atendimento_atual = $stmt_linha->fetch();

// Mapeamento numérico dos status para acender as bolinhas da Timeline
$passos = ['Agendado' => 1, 'Em Atendimento' => 2, 'Pronto para Retirada' => 3, 'Finalizado' => 4];
$passo_atual = $atendimento_atual ? $passos[$atendimento_atual['status']] : 0;
?>

<div class="py-2">
    <h2 class="fw-bold text-secondary mb-4">Espaço do Tutor</h2>

    <?php if ($atendimento_atual && $atendimento_atual['status'] !== 'Finalizado'): ?>
        <div class="card border-0 shadow-sm p-4 bg-white mb-4">
            <h5 class="fw-bold mb-1 text-primary"><i class="fa-solid fa-clock-rotate-left me-2"></i>Acompanhamento Ao Vivo</h5>
            <p class="text-muted small mb-4">Veja em tempo real o progresso do procedimento de <strong><?= $atendimento_atual['pet_nome'] ?></strong>.</p>
            
            <div class="alert alert-light border p-3 mb-4 d-flex justify-content-between">
                <div>
                    <span class="small text-muted d-block">Procedimento em execução:</span>
                    <strong class="text-dark"><?= $atendimento_atual['nome_servico'] ?></strong>
                </div>
                <div class="text-end">
                    <span class="small text-muted d-block">Profissional encarregado:</span>
                    <strong class="text-dark"><i class="fa-solid fa-user-tie me-1 text-secondary"></i> <?= $atendimento_atual['func_nome'] ?></strong>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center position-relative my-4 px-4 timeline-horizontal">
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 1 ? 'bg-primary text-white' : 'bg-light text-muted border' ?>" style="width: 40px; height: 40px; font-weight: bold;">1</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 1 ? 'fw-bold text-primary' : 'text-muted' ?>">Agendado</span>
                </div>
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 2 ? 'bg-warning text-dark font-weight-bold' : 'bg-light text-muted border' ?>" style="width: 40px; height: 40px; font-weight: bold;">2</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 2 ? 'fw-bold text-warning' : 'text-muted' ?>">Em Atendimento</span>
                </div>
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 3 ? 'bg-info text-dark font-weight-bold' : 'bg-light text-muted border' ?>" style="width: 40px; height: 40px; font-weight: bold;">3</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 3 ? 'fw-bold text-info' : 'text-muted' ?>">Pronto p/ Retirada</span>
                </div>
                <div class="text-center position-relative" style="z-index: 2;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto <?= $passo_atual >= 4 ? 'bg-success text-white' : 'bg-light text-muted border' ?>" style="width: 40px; height: 40px; font-weight: bold;">4</div>
                    <span class="small d-block mt-2 <?= $passo_atual >= 4 ? 'fw-bold text-success' : 'text-muted' ?>">Finalizado</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold text-secondary mb-3"><i class="fa-solid fa-paw me-2"></i>Meus Pets Registrados</h5>
    <div class="row row-cols-1 row-cols-md-3 g-3">
        <?php if(empty($meus_pets)): ?>
            <div class="col-12">
                <div class="alert alert-light border text-center py-4">Nenhum animal vinculado ao seu perfil de tutor.</div>
            </div>
        <?php else: foreach($meus_pets as $my_pet): ?>
            <div class="col">
                <div class="card border-0 shadow-sm bg-white">
                    <div class="card-body d-flex align-items-center">
                        <img src="../assets/uploads/<?= $my_pet['foto'] ?>" 
                             class="rounded-circle border" 
                             style="width: 65px; height: 65px; object-fit: cover;"
                             onerror="this.src='https://placehold.co/65x65?text=Pet'">
                        <div class="ms-3">
                            <h6 class="fw-bold mb-0 text-dark"><?= $my_pet['nome'] ?></h6>
                            <small class="text-muted d-block mb-1"><?= $my_pet['especie'] ?> • <?= $my_pet['raca'] ?></small>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.75rem;">Humor: <?= $my_pet['comportamento'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>