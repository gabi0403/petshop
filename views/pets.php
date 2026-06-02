<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

if ($_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: painel-tutor.php");
    exit;
}

// Busca todos os pets cadastrados juntamente com o nome do tutor
$sql = "SELECT p.*, u.nome as tutor_nome 
        FROM pets p 
        JOIN usuarios u ON p.cliente_id = u.id 
        ORDER BY p.nome ASC";
$pets = $pdo->query($sql)->fetchAll();

// Busca a lista de clientes para preencher o select do formulário de cadastro
$clientes = $pdo->query("SELECT id, nome FROM usuarios WHERE tipo = 'cliente' ORDER BY nome ASC")->fetchAll();
?>

<div class="py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-secondary">Manejo de Pacientes (Pets)</h2>
        <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#modalPet">
            <i class="fa-solid fa-plus me-1"></i> Cadastrar Novo Pet
        </button>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if(empty($pets)): ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted fs-5">Nenhum animal registrado no ecossistema.</p>
            </div>
        <?php else: foreach($pets as $pet): 
            // Define a cor do Badge baseado no comportamento
            $badge_color = 'bg-success';
            if ($pet['comportamento'] === 'Ansioso') $badge_color = 'bg-warning text-dark';
            if (str_contains($pet['comportamento'], 'Agressivo') || str_contains($pet['comportamento'], 'Cuidado')) $badge_color = 'bg-danger';
        ?>
            <div class="col">
                <div class="card h-100 border-0 shadow-sm overflow-hidden bg-white">
                    <div class="d-flex p-3 align-items-center">
                        <img src="../assets/uploads/<?= $pet['foto'] ?>" 
                             alt="Foto de <?= $pet['nome'] ?>" 
                             class="rounded-circle border" 
                             style="width: 80px; height: 80px; object-fit: cover;"
                             onerror="this.src='https://placehold.co/80x80?text=Pet'">
                        
                        <div class="ms-3">
                            <h5 class="fw-bold mb-1 text-dark"><?= $pet['nome'] ?></h5>
                            <span class="badge bg-light text-secondary border mb-2"><?= $pet['especie'] ?> (<?= $pet['raca'] ?>)</span>
                            <br>
                            <span class="badge <?= $badge_color ?>"><?= $pet['comportamento'] ?></span>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 px-3 py-2 d-flex justify-content-between align-items-center">
                        <small class="text-muted">Tutor: <strong><?= $pet['tutor_nome'] ?></strong></small>
                        <a href="../actions/pet-action.php?acao=deletar&id=<?= $pet['id'] ?>" 
                           class="text-danger small text-decoration-none" 
                           onclick="return confirm('Tem certeza que deseja remover este pet?')">
                            <i class="fa-solid fa-trash"></i> Excluir
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<div class="modal fade" id="modalPet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">Cadastrar Novo Paciente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <form action="../actions/pet-action.php?acao=cadastrar" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Responsável (Tutor)</label>
                        <select class="form-select" name="cliente_id" required>
                            <option value="">Selecione o proprietário...</option>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['nome'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nome do Pet</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Espécie</label>
                            <input type="text" class="form-control" name="especie" placeholder="ex: Cão, Cobra, Coruja" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Raça</label>
                            <input type="text" class="form-control" name="raca" placeholder="SRD se não souber">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Data de Nascimento</label>
                            <input type="date" class="form-control" name="data_nascimento">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perfil de Comportamento</label>
                        <select class="form-select" name="comportamento">
                            <option value="Amigável">Amigável / Dócil</option>
                            <option value="Normal" selected>Normal / Estável</option>
                            <option value="Ansioso">Ansioso / Medroso</option>
                            <option value="Requer Cuidado / Agressivo">Requer Cuidado / Reativo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto de Perfil do Animal</label>
                        <input type="file" class="form-control" name="foto" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Registro</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>