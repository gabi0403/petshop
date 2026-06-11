<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';
?>

<?php if (isset($_GET['erro']) && $_GET['erro'] === 'negado'): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-lg my-3 p-3 animate__animated animate__headShake" role="alert" style="border-left: 5px solid #dc3545 !important;">
        <div class="d-flex align-items-center">
            <div class="me-3 fs-3 text-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h5 class="alert-heading fw-bold mb-1">Acesso Restrito!</h5>
                <p class="mb-0 small text-secondary">Apenas usuários com o cargo de <strong>Gerente</strong> possuem autorização para resetar credenciais e senhas no sistema.</p>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php
// Garante que clientes/tutores não acessem métricas gerenciais
if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Varre os agendamentos "Finalizados" dentro do mês corrente usando a função nativa 'date_trunc' do PostgreSQL, somando o valor real cobrado por cada serviço.
$sql_faturamento = "SELECT SUM(s.preco) as total 
                    FROM agendamentos a 
                    JOIN servicos s ON a.servico_id = s.id 
                    WHERE a.status = 'Finalizado' 
                    AND a.data_hora >= date_trunc('month', current_date)";
$faturamento = $pdo->query($sql_faturamento)->fetch()['total'] ?? 0;

// Converte a marcação timestamp para o formato de data simples e conta quantas ordens de serviço estão agendadas ou em andamento pra hoje.
$sql_hoje = "SELECT COUNT(*) as total FROM agendamentos WHERE data_hora::date = CURRENT_DATE";
$atendimentos_hoje = $pdo->query($sql_hoje)->fetch()['total'];


// Agrupa os atendimentos pelas grandes categorias do mercado ('clinica', 'estetica') mapeando onde a equipe tá concentrando seus esforços.
$sql_grafico = "SELECT s.categoria, COUNT(a.id) as total 
                FROM agendamentos a
                JOIN servicos s ON a.servico_id = s.id
                GROUP BY s.categoria
                ORDER BY total DESC";
$dados_grafico = $pdo->query($sql_grafico)->fetchAll();

// Prepara os arrays limpos que o Js vai ler para desenhar o gráfico
$labels_categorias = [];
$valores_categorias = [];
foreach ($dados_grafico as $row) {
    // Força a primeira letra a ficar maiúscula por estética na tela
    $labels_categorias[] = ucfirst($row['categoria']);
    $valores_categorias[] = $row['total'];
}

// Uma subquery negativa: localiza pets que não possuem nenhum registro de atendimento nos últimos 20 dias, trazendo os dados de contato direto do tutor.
$sql_sumidos = "SELECT p.nome as pet_nome, p.especie, u.nome as tutor_nome, u.telefone 
                FROM pets p
                JOIN usuarios u ON p.cliente_id = u.id
                WHERE p.id NOT IN (
                    SELECT pet_id FROM agendamentos 
                    WHERE data_hora > CURRENT_DATE - INTERVAL '20 days'
                ) LIMIT 4"; // Limitado a 4 para manter o design flexível e elegante
$pets_sumidos = $pdo->query($sql_sumidos)->fetchAll();

// Coleta as 3 últimas notas publicadas no mural interno para triagem imediata da equipe.
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
                        <h6 class="text-muted mb-0">Principal Demanda</h6>
                        <h4 class="fw-bold mb-0" style="font-size: 1.1rem;"><?= $labels_categorias[0] ?? 'Sem dados' ?></h4>
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
                    <?php if(empty($labels_categorias)): ?>
                        <p class="text-muted text-center py-5">Nenhum atendimento registrado para exibir o gráfico.</p>
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
                        // Sistema dinâmico que altera as cores do card baseado na gravidade do banco
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
                <h5 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-bell text-warning me-2"></i>Alerta de Retorno Ativo</h5>
                <p class="text-muted small mb-3">Estes pacientes não visitam o estabelecimento há mais de 20 dias. Clique no ícone verde para iniciar uma conversa de retorno.</p>
                
                <div class="row row-cols-1 row-cols-md-2 g-2">
                    <?php if(empty($pets_sumidos)): ?>
                        <p class="text-muted italic px-3">Excelente! Todos os pacientes ativos e frequentes nos últimos dias.</p>
                    <?php else: foreach($pets_sumidos as $pet): ?>
                        <div class="col">
                            <div class="p-3 border rounded bg-light d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= htmlspecialchars($pet['pet_nome']) ?> <span class="badge bg-secondary fw-normal font-monospace" style="font-size:0.7rem;"><?= htmlspecialchars($pet['especie']) ?></span></h6>
                                    <small class="text-muted d-block mt-1">Tutor(a): <?= htmlspecialchars($pet['tutor_nome']) ?></small>
                                </div>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $pet['telefone']) ?>?text=Olá%20<?= urlencode($pet['tutor_nome']) ?>,%20notamos%20que%20o%20<?= urlencode($pet['pet_nome']) ?>%20está%20há%20algum%20tempo%20sem%20nos%20visitar!%20Deseja%20agendar%20um%20horário%20para%20cuidados?" 
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
    const canvasServicos = document.getElementById('graficoServicos');
    
    if (canvasServicos) {
        // Converte os arrays do PHP em vetores limpos legíveis pelo Js
        const labelsBanco = <?php echo json_encode($labels_categorias); ?>;
        const valoresBanco = <?php echo json_encode($valores_categorias); ?>;

        new Chart(canvasServicos.getContext('2d'), {
            type: 'doughnut', // gráfico estilo pizza
            data: {
                labels: labelsBanco,
                datasets: [{
                    data: valoresBanco,
                    backgroundColor: ['#3498db', '#2ecc71', '#9b59b6', '#f1c40f'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, font: { size: 12 } }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>