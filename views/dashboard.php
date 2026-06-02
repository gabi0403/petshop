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

// --- 3. CONSULTA: SERVIÇO MAIS PROCURADO ---
$sql_destaque = "SELECT s.nome_servico, COUNT(a.id) as qtd 
                 FROM agendamentos a 
                 JOIN servicos s ON a.servico_id = s.id 
                 GROUP BY s.nome_servico 
                 ORDER BY qtd DESC LIMIT 1";
$servico_destaque = $pdo->query($sql_destaque)->fetch()['nome_servico'] ?? 'Nenhum';

// --- 4. CONSULTA: ALERTA DE RETORNO (Pets sumidos há +20 dias) ---
// Buscamos pets cujo último agendamento finalizado foi há mais de 20 dias
$sql_sumidos = "SELECT p.nome as pet_nome, p.especie, u.nome as tutor_nome, u.telefone 
                FROM pets p
                JOIN usuarios u ON p.cliente_id = u.id
                WHERE p.id NOT IN (
                    SELECT pet_id FROM agendamentos 
                    WHERE data_hora > CURRENT_DATE - INTERVAL '20 days'
                ) LIMIT 5";
$pets_sumidos = $pdo->query($sql_sumidos)->fetchAll();
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
                        <h6 class="text-muted mb-0">Serviço Mais Procurado</h6>
                        <h4 class="fw-bold mb-0" style="font-size: 1.1rem;"><?= $servico_destaque ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold mb-4 text-secondary">Evolução de Atendimentos</h5>
                <canvas id="graficoFaturamento" height="150"></canvas>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-bell text-warning me-2"></i>Alerta de Retorno</h5>
                <p class="text-muted small mb-4">Pets ausentes há mais de 20 dias.</p>
                
                <div class="list-group list-group-flush">
                    <?php if(empty($pets_sumidos)): ?>
                        <p class="text-muted italic">Nenhum pet pendente de retorno.</p>
                    <?php else: foreach($pets_sumidos as $pet): ?>
                        <div class="list-group-item px-0 border-0 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= $pet['pet_nome'] ?> <span class="badge bg-light text-dark fw-normal"><?= $pet['especie'] ?></span></h6>
                                    <small class="text-muted">Tutor: <?= $pet['tutor_nome'] ?></small>
                                </div>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $pet['telefone']) ?>?text=Olá%20<?= $pet['tutor_nome'] ?>,%20notamos%20que%20o%20<?= $pet['pet_nome'] ?>%20está%20há%20algum%20tempo%20sem%20nos%20visitar!%20Deseja%20agendar%20um%20horário?" 
                                   target="_blank" class="btn btn-success btn-sm rounded-pill">
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('graficoFaturamento').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                datasets: [{
                    label: 'Atendimentos por Dia',
                    data: [12, 19, 15, 25, 22, 30], // Dados fictícios para o gráfico
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>