<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo   = trim($_POST['titulo']);
    $conteudo = trim($_POST['conteudo']);
    $user_id  = $_SESSION['usuario_id'];

    try {
        $sql = "INSERT INTO mural_avisos (usuario_id, titulo, conteudo) VALUES (:user_id, :titulo, :conteudo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id'  => $user_id,
            ':titulo'   => $titulo,
            ':conteudo' => $conteudo
        ]);

        header("Location: ../views/mural.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao fixar aviso: " . $e->getMessage());
    }
}

if ($acao === 'deletar') {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM mural_avisos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../views/mural.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao remover aviso: " . $e->getMessage());
    }
}