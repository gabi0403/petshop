<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

// --- CADASTRAR CLIENTE ---
if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $senha    = trim($_POST['senha']);

    // Criptografa a senha usando o algoritmo seguro padrão do PHP
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo, cargo) 
                VALUES (:nome, :email, :senha, :telefone, 'cliente', 'Cliente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'     => $nome,
            ':email'    => $email,
            ':senha'    => $senha_hash,
            ':telefone' => $telefone
        ]);

        // Grava no Log de auditoria
        $log = $pdo->prepare("INSERT INTO log_atividades (usuario_id, acao) VALUES (:uid, :acao)");
        $log->execute([
            ':uid'  => $_SESSION['usuario_id'],
            ':acao' => "Cadastrou o novo cliente tutor: '$nome' (Email: $email)."
        ]);

        header("Location: ../views/clientes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao cadastrar cliente: " . $e->getMessage());
    }
}

// --- DELETAR CLIENTE ---
if ($acao === 'deletar') {
    $id = $_GET['id'];
    try {
        // Grava quem deletou antes de efetuar a remoção
        $log = $pdo->prepare("INSERT INTO log_atividades (usuario_id, acao) VALUES (:uid, :acao)");
        $log->execute([
            ':uid'  => $_SESSION['usuario_id'],
            ':acao' => "Removeu o cliente de ID #$id do sistema."
        ]);

        // Executa a deleção (os pets somem junto devido ao ON DELETE CASCADE do banco)
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id AND tipo = 'cliente'");
        $stmt->execute([':id' => $id]);

        header("Location: ../views/clientes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao deletar cliente: " . $e->getMessage());
    }
}