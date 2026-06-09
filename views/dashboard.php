<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

// Bloqueio de segurança: apenas a equipe acessa o Dashboard
if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// --- 1. CONSULTA: FATURAMENTO MENSAL ---
$sql_faturamento = "SELECT SUM(s.preco) as total 
                    FROM agendamentos a 
                    JOIN servicos s ON a.servico_id = s.id 
                    WHERE a.status = 'Finalizado' 
                    AND a.data_hora >= date_trunc('month', current_date)";
$faturamento = $pdo->query($sql_faturamento)->fetch()['total'] ?? 0;

// --- 2. CONSULTA: ATENDIMENTOS HOJE ---
$sql_hoje = "SELECT COUNT(*) as total FROM agendamentos WHERE data_hora::date = CURRENT_DATE";
$atendimentos_hoje = $pdo->query($sql_hoje)->fetch()['total'];

// --- 3. CONSULTA: SERVIÇO MAIS PROCURADO (Para alimentar o gráfico) ---
$sql_grafico = "SELECT s.nome_servico, COUNT(a.id) as total 
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                GROUP BY s.nome_servico
                ORDER BY total DESC LIMIT 5";
$dados_grafico = $pdo->query($sql_grafico)->fetchAll();

$labels_servicos = [];
$valores_servicos = [];
foreach ($dados_grafico as $row) {
    $labels_servicos[] = $row['nome_servico'];
    $valores_servicos[] = $row['total'];
}

// --- 4. CONSULTA: ALERTA DE RETORNO (Pets sumidos há +20 dias) ---
$sql_sumidos = "SELECT p.nome as pet_nome, p.especie, u.nome as tutor_nome, u.telefone 
                FROM pets p
                JOIN usuarios u ON p.cliente_id = u.id
                WHERE p.id NOT IN (
                    SELECT pet_id FROM agendamentos 
                    WHERE data_hora > CURRENT_DATE - INTERVAL '20 days'
                ) LIMIT 5";
$pets_sumidos = $pdo->query($sql_sumidos)->fetchAll();

// --- 5. CONSULTA: ÚLTIMOS AVISOS DO MURAL PARA RESUMO ---
$sql_mural = "SELECT m.*, u.nome as autor_nome 
              FROM mural_avisos m
              LEFT JOIN usuarios u ON m.usuario_id = u.id 
              ORDER BY m.data_publicacao DESC LIMIT 3";
$avisos_mural = $pdo->query($sql_mural)->fetchAll();
?>

<div class="py-2">
    <h2 class="fw-bold text-secondary mb-4">Dashboard Gerencial</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-light-success text-success p-3 rounded-circle me-3">
                        <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Faturamento Mensal</h6>
                        <h4 class="fw-bold mb-0">R$ <?= number_format($faturamento, 2, ',', '.') ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-light-primary text-primary p-3 rounded-circle me-3">
                        <i class="fa-solid fa-calendar-check fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Atendimentos Hoje</h6>
                        <h4 class="fw-bold mb-0"><?= $atendimentos_hoje ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-light-warning text-warning p-3 rounded-circle me-3">
                        <i class="fa-solid fa-star fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0">Mais Procurado</h6>
                        <h4 class="fw-bold mb-0" style="font-size: 1.1rem;"><?= $labels_servicos[0] ?? 'Nenhum' ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-chart-pie me-2 text-muted"></i>Distribuição de Serviços</h5>
                <div style="position: relative; height: 230px; width: 100%;">
                    <?php if(empty($labels_servicos)): ?>
                        <p class="text-muted text-center py-5">Sem dados de serviços suficientes.</p>
                    <?php else: ?>
                        <canvas id="graficoServicos"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-secondary"><i class="fa-solid fa-chalkboard me-2 text-muted"></i>Avisos do Plantão</h5>
                    <a href="mural.php" class="btn btn-sm btn-outline-secondary rounded-pill">Ver Quadro Completo</a>
                </div>
                <div class="mural-lista">
                    <?php if(empty($avisos_mural)): ?>
                        <p class="text-muted text-center py-4">Nenhum aviso ativo no mural.</p>
                    <?php else: foreach($avisos_mural as $aviso): 
                        // Determinação de cor baseada no novo campo de urgência
                        switch($aviso['urgencia'] ?? 'baixa') {
                            case 'alta':
                                $cor_classe = 'alert-danger border-danger text-danger-dark';
                                $icone = '<i class="fa-solid fa-triangle-exclamation text-danger animate__animated animate__flash animate__infinite me-2"></i>';
                                break;
                            case 'media':
                                $cor_classe = 'alert-warning border-warning text-warning-dark';
                                $icone = '<i class="fa-solid fa-circle-exclamation text-warning me-2"></i>';
                                break;
                            default:
                                $cor_classe = 'alert-info border-info text-info-dark';
                                $icone = '<i class="fa-solid fa-circle-info text-info me-2"></i>';
                                break;
                        }
                    ?>
                        <div class="alert <?= $cor_classe ?> border-start border-4 p-2 mb-2 shadow-sm small d-flex justify-content-between align-items-center">
                            <div>
                                <?= $icone ?>
                                <strong><?= htmlspecialchars($aviso['titulo']) ?>:</strong> 
                                <span><?= htmlspecialchars(mb_strimwidth($aviso['conteudo'], 0, 45, '...')) ?></span>
                            </div>
                            <small class="text-muted ms-2 font-monospace" style="font-size:0.7rem;"><?= date('d/m H:i', strtotime($aviso['data_publicacao'])) ?></small>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-bell text-warning me-2"></i>Alerta de Retorno</h5>
                <p class="text-muted small mb-3">Pets ausentes há mais de 20 dias (Clique no ícone para iniciar ação preventiva de re-fidelização).</p>
                
                <div class="row row-cols-1 row-cols-md-2 g-2">
                    <?php if(empty($pets_sumidos)): ?>
                        <p class="text-muted italic px-3">Nenhum pet pendente de retorno.</p>
                    <?php else: foreach($pets_sumidos as $pet): ?>
                        <div class="col">
                            <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= $pet['pet_nome'] ?> <span class="badge bg-secondary fw-normal font-monospace" style="font-size:0.7rem;"><?= $pet['especie'] ?></span></h6>
                                    <small class="text-muted d-block mt-1">Tutor(a): <?= $pet['tutor_nome'] ?></small>
                                </div>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $pet['telefone']) ?>?text=Olá%20<?= $pet['tutor_nome'] ?>,%20notamos%20que%20o%20<?= $pet['pet_nome'] ?>%20está%20há%20algum%20tempo%20sem%20nos%20visitar!%20Deseja%20agendar%20um%20horário?" 
                                   target="_blank" class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width:35px; height:35px;">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficoServicos');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($labels_servicos) ?>,
                    datasets: [{
                        data: <?= json_encode($valores_servicos) ?>,
                        backgroundColor: ['#2ecc71', '#3498db', '#e67e22', '#9b59b6', '#f1c40f'],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        }
    });
</script>

<?php require_once '../includes/footer.php'; ?>