<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Busca todos os usuários clientes e conta quantos pets cada um tem cadastrado
$sql = "SELECT u.id, u.nome, u.email, u.telefone, u.criado_em, COUNT(p.id) as total_pets
        FROM usuarios u
        LEFT JOIN pets p ON p.cliente_id = u.id
        WHERE u.tipo = 'cliente'
        GROUP BY u.id, u.nome, u.email, u.telefone, u.criado_em
        ORDER BY u.nome ASC";
$clientes = $pdo->query($sql)->fetchAll();
?>

<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-secondary mb-1">Controle de Clientes (Tutores)</h2>
            <p class="text-muted small">Gerenciamento de tutores integrados ao ecossistema.</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalCliente">
            <i class="fa-solid fa-user-plus me-1"></i> Adicionar Cliente
        </button>
    </div>

    <div class="card border-0 shadow-sm p-3 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nome Completo</th>
                        <th>E-mail de Contato</th>
                        <th>Telefone / WhatsApp</th>
                        <th class="text-center">Pets Vinculados</th>
                        <th>Data de Cadastro</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($clientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhum cliente tutor cadastrado na base de dados.</td>
                        </tr>
                    <?php else: foreach($clientes as $cliente): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($cliente['nome']) ?></td>
                            <td><?= htmlspecialchars($cliente['email']) ?></td>
                            <td><?= htmlspecialchars($cliente['telefone']) ?: '<span class="text-muted small">Não informado</span>' ?></td>
                            <td class="text-center">
                                <span class="badge bg-info text-dark fw-bold rounded-pill px-3">
                                    <?= $cliente['total_pets'] ?> <?= $cliente['total_pets'] == 1 ? 'pet' : 'pets' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($cliente['criado_em'])) ?></td>
                            <td class="text-center">
                                <a href="../actions/cliente-action.php?acao=deletar&id=<?= $cliente['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('ATENÇÃO: Deletar este cliente apagará AUTOMATICAMENTE todos os seus pets e agendamentos vinculados devido à integridade do banco. Confirmar?')">
                                    <i class="fa-solid fa-user-xmark"></i> Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Cadastrar Novo Tutor</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal"></button>
            </div>
            <form action="../actions/cliente-action.php?acao=cadastrar" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" placeholder="Ex: João Silva" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">E-mail de Acesso</label>
                        <input type="email" class="form-control" name="email" placeholder="Ex: joao@gmail.com" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Telefone / WhatsApp</label>
                            <input type="text" class="form-control" name="telefone" placeholder="Ex: 11999999999" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Senha Inicial do Cliente</label>
                            <input type="password" class="form-control" name="senha" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>