<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_servico = trim($_POST['nome_servico']);
    $categoria    = $_POST['categoria'];
    $preco        = $_POST['preco'];

    try {
        $sql = "INSERT INTO servicos (nome_servico, categoria, preco) VALUES (:nome, :categoria, :preco)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome_servico,
            ':categoria' => $categoria,
            ':preco'     => $preco
        ]);

        header("Location: ../views/servicos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao cadastrar serviço: " . $e->getMessage());
    }
}

if ($acao === 'deletar') {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../views/servicos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao remover serviço: " . $e->getMessage());
    }
}