<?php
session_start();
require_once '../config/conexao.php';

// Bloqueio de segurança
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

// ================= AÇÕES PARA SERVIÇOS =================

if ($acao === 'cadastrar_servico' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_servico = trim($_POST['nome_servico']);
    $categoria    = trim($_POST['categoria']); // RECOLHENDO A CATEGORIA
    $preco        = floatval($_POST['preco']);

    try {
        $sql = "INSERT INTO servicos (nome_servico, categoria, preco) VALUES (:nome, :categoria, :preco)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'      => $nome_servico, 
            ':categoria' => $categoria,
            ':preco'     => $preco
        ]);
        
        header("Location: ../views/configuracoes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao cadastrar serviço: " . $e->getMessage());
    }
}

if ($acao === 'editar_servico' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['id']);
    $preco = floatval($_POST['preco']);

    try {
        $stmt = $pdo->prepare("UPDATE servicos SET preco = :preco WHERE id = :id");
        $stmt->execute([':preco' => $preco, ':id' => $id]);
        
        header("Location: ../views/configuracoes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar preço: " . $e->getMessage());
    }
}

if ($acao === 'deletar_servico') {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM servicos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        header("Location: ../views/configuracoes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao remover serviço: " . $e->getMessage());
    }
}


// ================= AÇÕES PARA EQUIPE =================

if ($acao === 'cadastrar_equipe' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cargo = trim($_POST['cargo']);
    
    // Define a senha padrão criptografada '123456'
    $senha_padrao = password_hash('123456', PASSWORD_DEFAULT);

    try {
        // Insere na sua tabela real de usuários definindo o tipo como 'equipe'
        $sql = "INSERT INTO usuarios (nome, email, senha, cargo, tipo) VALUES (:nome, :email, :senha, :cargo, 'equipe')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $senha_padrao,
            ':cargo' => $cargo
        ]);
        
        header("Location: ../views/configuracoes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao cadastrar membro da equipe: " . $e->getMessage());
    }
}

if ($acao === 'deletar_equipe') {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id AND tipo = 'equipe'");
        $stmt->execute([':id' => $id]);
        
        header("Location: ../views/configuracoes.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao remover membro da equipe: " . $e->getMessage());
    }
}