<?php
require_once '../includes/header.php';
require_once '../config/conexao.php';

// Mensagens de feedback para o usuário
$mensagem = '';
$tipo_mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $usuario_id = $_SESSION['usuario_id']; // Pega o ID do usuário logado na sessão

    // 1. Validação básica de preenchimento
    if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_mensagem = "danger";
    } 
    // 2. Verifica se a nova senha coincide com a confirmação
    elseif ($nova_senha !== $confirmar_senha) {
        $mensagem = "A nova senha e a confirmação não são iguais.";
        $tipo_mensagem = "danger";
    } 
    // 3. Validação de tamanho mínimo para evitar senhas fracas 
    elseif (strlen($nova_senha) < 6) {
        $mensagem = "A nova senha deve ter pelo menos 6 caracteres.";
        $tipo_mensagem = "danger";
    } else {
        // Busca a senha atual criptografada no banco para validação
        $sql = "SELECT senha FROM usuarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $usuario_id]);
        $usuario = $stmt->fetch();

        // 4. Segurança: Verifica se a senha atual digitada está correta
        if ($usuario && password_verify($senha_atual, $usuario['senha'])) {
            
            // 5. Criptografia Blindada: Gera o novo hash seguro
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

            // Atualiza o banco de dados
            $sql_update = "UPDATE usuarios SET senha = :senha WHERE id = :id";
            $stmt_update = $pdo->prepare($sql_update);
            $stmt_update->execute([
                'senha' => $nova_senha_hash,
                'id' => $usuario_id
            ]);

            $mensagem = "Senha alterada com sucesso! Da próxima vez, use suas novas credenciais.";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "A senha atual informada está incorreta.";
            $tipo_mensagem = "danger";
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
                <h4 class="fw-bold text-secondary mb-2">
                    <i class="fa-solid fa-key text-warning me-2"></i>Alterar Minha Senha
                </h4>
                <p class="text-muted small mb-4">Mantenha sua conta segura atualizando sua credencial de acesso periodicamente.</p>

                <!-- Exibição de alertas dinâmicos do PHP -->
                <?php if (!empty($mensagem)): ?>
                    <div class="alert alert-<?= $tipo_mensagem ?> alert-dismissible fade show" role="alert">
                        <?= $mensagem ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="alterar-senha.php" method="POST">
                    <!-- Campo Senha Atual -->
                    <div class="mb-3">
                        <label for="senha_atual" class="form-label fw-medium text-dark">Senha Atual</label>
                        <input type="password" class="form-control p-2.5" id="senha_atual" name="senha_atual" required placeholder="Digite sua senha de acesso atual">
                    </div>

                    <hr class="my-4 text-muted opacity-25">

                    <!-- Campo Nova Senha -->
                    <div class="mb-3">
                        <label for="nova_senha" class="form-label fw-medium text-dark">Nova Senha</label>
                        <input type="password" class="form-control p-2.5" id="nova_senha" name="nova_senha" required placeholder="Mínimo de 6 caracteres">
                    </div>

                    <!-- Campo Confirmação -->
                    <div class="mb-4">
                        <label for="confirmar_senha" class="form-label fw-medium text-dark">Confirme a Nova Senha</label>
                        <input type="password" class="form-control p-2.5" id="confirmar_senha" name="confirmar_senha" required placeholder="Repita a nova senha">
                    </div>

                    <!-- Botões de Ação -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill shadow-sm">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Salvar Nova Senha
                        </button>
                        <a href="dashboard.php" class="btn btn-light px-4 rounded-pill border">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>