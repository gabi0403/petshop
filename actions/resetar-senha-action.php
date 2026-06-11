<?php
session_start();
require_once '../config/conexao.php';

// 1. apenas funcionários logados com cargo de 'Gerente' podem resetar senhas
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_cargo'] !== 'Gerente') {
    header("Location: ../views/dashboard.php?erro=negado");
    exit;
}

// 2. verifica se o ID do usuário que esquecer a senha foi enviado
if (isset($_GET['id'])) {
    $usuario_id = intval($_GET['id']);

    // O gerente não pode resetar a própria senha por essa tela
    if ($usuario_id === intval($_SESSION['usuario_id'])) {
        header("Location: ../views/configuracoes.php?erro=auto_reset");
        exit;
    }

    // 3. define a nova senha padrão temporária
    $senha_padrao = '123456';
    $nova_senha_hash = password_hash($senha_padrao, PASSWORD_DEFAULT);

    try {
        // Executa a atualização limpa no banco do PostgreSQL
        $sql = "UPDATE usuarios SET senha = :senha WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'senha' => $nova_senha_hash,
            'id' => $usuario_id
        ]);

        // Retorna para a tela de configurações com uma mensagem de sucesso
        header("Location: ../views/configuracoes.php?sucesso=senha_resetada");
        exit;

    } catch (PDOException $e) {
        // Se der algum erro de banco, aborta 
        header("Location: ../views/configuracoes.php?erro=banco");
        exit;
    }
} else {
    header("Location: ../views/configuracoes.php");
    exit;
}