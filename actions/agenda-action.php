<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

// --- CRIAR AGENDAMENTO ---
if ($acao === 'agendar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_id         = $_POST['pet_id'];
    $servico_id     = $_POST['servico_id'];
    $funcionario_id = $_POST['funcionario_id'];
    $data_hora      = $_POST['data_hora'];

    try {
        $sql = "INSERT INTO agendamentos (pet_id, servico_id, funcionario_id, data_hora) 
                VALUES (:pet_id, :servico_id, :funcionario_id, :data_hora)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pet_id'         => $pet_id,
            ':servico_id'     => $servico_id,
            ':funcionario_id' => $funcionario_id,
            ':data_hora'      => $data_hora
        ]);

        // Log da criação
        $log = $pdo->prepare("INSERT INTO log_atividades (usuario_id, acao) VALUES (:uid, :acao)");
        $log->execute([
            ':uid'  => $_SESSION['usuario_id'],
            ':acao' => "Inseriu um novo agendamento na esteira para o pet ID $pet_id em " . date('d/m/H:i', strtotime($data_hora))
        ]);

        header("Location: ../views/agendamentos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao criar agendamento: " . $e->getMessage());
    }
}

// --- ALTERAR STATUS  ---
if ($acao === 'alterar_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $agenda_id   = $_POST['agenda_id'];
    $novo_status = $_POST['novo_status'];

    try {
        // Atualiza o status
        $sql = "UPDATE agendamentos SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $novo_status,
            ':id'     => $agenda_id
        ]);

        // registra qual funcionário moveu o pet na esteira
        $log = $pdo->prepare("INSERT INTO log_atividades (usuario_id, acao) VALUES (:uid, :acao)");
        $log->execute([
            ':uid'  => $_SESSION['usuario_id'],
            ':acao' => $_SESSION['usuario_nome'] . " ({$_SESSION['usuario_cargo']}) moveu o status do atendimento #$agenda_id para '$novo_status'."
        ]);

        header("Location: ../views/agendamentos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar status: " . $e->getMessage());
    }
}

// --- DELETAR AGENDAMENTO ---
if ($acao === 'deletar') {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../views/agendamentos.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao remover agendamento: " . $e->getMessage());
    }
}