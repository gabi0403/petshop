<?php
// Inicia a sessão do PHP para conseguirmos lembrar quem logou
session_start();

// Importa a conexão com o banco de dados
require_once '../config/conexao.php';

// Verifica se a requisição veio de forma correta via formulário (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Captura e limpa os espaços em branco dos dados recebidos
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Validação básica de segurança
    if (empty($email) || empty($senha)) {
        header("Location: ../views/login.php?erro=campos_vazios");
        exit;
    }

    try {
        // Prepara a query SQL usando Prepared Statements (Proteção contra SQL Injection)
        $sql = "SELECT id, nome, email, senha, tipo, cargo FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        // Busca o resultado da consulta
        $usuario = $stmt->fetch();

        // Se o usuário existir, vamos testar a senha criptografada
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            
            // login bem-sucedido, gravamos as informações do "crachá" na sessão
            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_tipo']  = $usuario['tipo'];   // 'equipe' ou 'cliente'
            $_SESSION['usuario_cargo'] = $usuario['cargo'];  // 'Veterinário', 'Tosador', etc.

            // Grava uma linha na nossa tabela de Log
            $log_sql = "INSERT INTO log_atividades (usuario_id, acao) VALUES (:id, :acao)";
            $log_stmt = $pdo->prepare($log_sql);
            $log_stmt->execute([
                ':id'   => $usuario['id'],
                ':acao' => "O usuário " . $usuario['nome'] . " ({$usuario['cargo']}) realizou login no sistema."
            ]);

            // Redirecionamento baseado no tipo de acesso
            if ($usuario['tipo'] === 'equipe') {
                header("Location: ../views/dashboard.php");
            } else {
                header("Location: ../views/painel-tutor.php");
            }
            exit;

        } else {
            // E-mail existente mas senha errada, ou usuário não encontrado
            header("Location: ../views/login.php?erro=dados_invalidos");
            exit;
        }

    } catch (PDOException $e) {
        // Se houver algum erro de banco, interrompe e exibe
        die("Erro ao processar o login: " . $e->getMessage());
    }

} else {
    // Se o usuário tentar acessar esse arquivo direto pela URL, chuta ele de volta pro login XD)
    header("Location: ../views/login.php");
    exit;
}