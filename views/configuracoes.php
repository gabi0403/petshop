<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// 1. Busca todos os serviços cadastrados
$servicos = $pdo->query("SELECT * FROM servicos ORDER BY nome_servico ASC")->fetchAll();

// 2. Busca todos os membros da equipe (usuarios do tipo equipe)
$equipe = $pdo->query("SELECT id, nome, email, cargo, tipo FROM usuarios WHERE tipo = 'equipe' ORDER BY nome ASC")->fetchAll();
?>
<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'senha_resetada'): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-check text-success me-2"></i> <strong>Senha resetada com sucesso!</strong> A nova credencial temporária deste usuário é <code>123456</code>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fa-solid fa-circle-xmark text-danger me-2"></i> 
        <strong>Erro na operação:</strong> 
        <?php 
            if($_GET['erro'] === 'auto_reset') echo "Você não pode resetar a sua própria senha. Use o menu 'Senha' no topo do site.";
            else echo "Não foi possível completar o reset. Tente novamente.";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="py-2">
    <div class="mb-4">
        <h2 class="fw-bold text-secondary mb-1">Configurações do Sistema</h2>
        <p class="text-muted small">Gerencie os serviços prestados, preços e a equipe de colaboradores da clínica.</p>
    </div>

    <ul class="nav nav-tabs mb-4 border-bottom-0" id="configTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold border-0 rounded-pill me-2 px-4 shadow-sm" id="servicos-tab" data-bs-toggle="tab" data-bs-target="#servicos-pane" type="button" role="tab"><i class="fa-solid fa-scissors me-2"></i>Serviços e Preços</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold border-0 rounded-pill px-4 shadow-sm" id="equipe-tab" data-bs-toggle="tab" data-bs-target="#equipe-pane" type="button" role="tab"><i class="fa-solid fa-users me-2"></i>Equipe / Colaboradores</button>
        </li>
    </ul>

    <div class="tab-content" id="configTabsContent">
        
        <div class="tab-pane fade show active" id="servicos-pane" role="tabpanel" tabindex="0">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-secondary mb-0">Catálogo de Serviços</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNovoServico">
                        <i class="fa-solid fa-plus me-1"></i> Novo Serviço
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome do Serviço / Procedimento</th>
                                <th style="width: 200px;">Preço Sugerido</th>
                                <th class="text-center" style="width: 150px;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($servicos as $s): ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($s['nome_servico']) ?></td>
                                    <td class="text-success fw-bold">R$ <?= number_format($s['preco'], 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary me-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarServico<?= $s['id'] ?>" title="Editar Preço">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <a href="../actions/config-action.php?acao=deletar_servico&id=<?= $s['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Excluir este serviço impedirá novos agendamentos para ele. Confirmar?')" title="Excluir">
                                             <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEditarServico<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header bg-dark text-white">
                                                <h6 class="modal-title fw-bold">Ajustar Preço</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="../actions/config-action.php?acao=editar_servico" method="POST">
                                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Serviço</label>
                                                        <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($s['nome_servico']) ?>" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold">Novo Valor (R$)</label>
                                                        <input type="number" step="0.01" class="form-control" name="preco" value="<?= $s['preco'] ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer p-2 bg-light">
                                                    <button type="submit" class="btn btn-sm btn-success w-100">Salvar Alteração</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="equipe-pane" role="tabpanel" tabindex="0">
            <div class="card border-0 shadow-sm p-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-secondary mb-0">Funcionários</h5>
                    <button class="btn btn-sm btn-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNovaEquipe">
                        <i class="fa-solid fa-user-plus me-1"></i> Adicionar Membro
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome Completo</th>
                                <th>E-mail (Login)</th>
                                <th>Cargo / Função</th>
                                <th class="text-center" style="width: 220px;">Ações de Segurança</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($equipe as $e): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded-circle text-secondary me-2" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                                                <i class="fa-solid fa-user-doctor"></i>
                                            </div>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($e['nome']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted font-monospace small"><?= htmlspecialchars($e['email']) ?></td>
                                    <td><span class="badge bg-light text-secondary border"><?= htmlspecialchars($e['cargo']) ?></span></td>
                                    <td class="text-center">
                                        <?php if($e['id'] != $_SESSION['usuario_id']): ?>
                                            
                                            <a href="../actions/resetar-senha-action.php?id=<?= $e['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning me-1" 
                                               title="Resetar Senha para Padrão (123456)"
                                               onclick="return confirm('Tem certeza que deseja resetar a senha de <?= htmlspecialchars($e['nome']) ?> para o padrão (123456)?')">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </a>

                                            <a href="../actions/config-action.php?acao=deletar_equipe&id=<?= $e['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               title="Remover Acessos"
                                               onclick="return confirm('Deseja mesmo remover os acessos deste funcionário?')">
                                                <i class="fa-solid fa-user-minus"></i>
                                            </a>
                                            
                                        <?php else: ?>
                                            <span class="text-muted small italic">Sua Conta (Gerenciar no Topo)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalNovoServico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Cadastrar Novo Serviço</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/config-action.php?acao=cadastrar_servico" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome do Serviço</label>
                        <input type="text" class="form-control" name="nome_servico" placeholder="Ex: Tosa Higiênica, Consulta Especialista" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" name="categoria" required>
                            <option value="clinica">🏥 Clínica / Consulta</option>
                            <option value="estetica">🧼 Estética / Banho e Tosa</option>
                            <option value="outros">📦 Outros / Serviços Gerais</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Preço de Venda (R$)</label>
                        <input type="number" step="0.01" class="form-control" name="preco" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar no Catálogo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovaEquipe" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Adicionar Profissional à Equipe</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="../actions/config-action.php?acao=cadastrar_equipe" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" placeholder="Ex: Dr. Ricardo Silva" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail (Será o Login de Acesso)</label>
                        <input type="email" class="form-control" name="email" placeholder="nome@petshop.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cargo / Especialidade</label>
                        <select class="form-select" name="cargo" required>
                            <option value="Veterinário">🩺 Veterinário</option>
                            <option value="Auxiliar de Veterinário">🩹 Auxiliar de Veterinário</option>
                            <option value="Tosador">✂️ Tosador</option>
                            <option value="Banhista">🧼 Banhista</option>
                            <option value="Adestrador">🐕 Adestrador</option>
                            <option value="Atendente">📞 Atendente</option>
                            <option value="Recepcionista">🏢 Recepcionista</option>
                            <option value="Gerente">📊 Gerente</option>
                        </select>
                    </div>
                    <div class="p-2 bg-light text-muted border rounded small">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i> A senha inicial padrão para novos membros será obrigatoriamente <strong>123456</strong>.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark">Confirmar Contratação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link { color: #7f8c8d; background-color: #f8f9fa; }
    .nav-tabs .nav-link.active { color: #fff !important; background-color: #2c3e50 !important; }
</style>

<?php require_once '../includes/footer.php'; ?>