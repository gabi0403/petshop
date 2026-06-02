<?php
session_start();
require_once '../config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'equipe') {
    header("Location: ../views/login.php");
    exit;
}

$acao = $_GET['acao'] ?? '';

// --- OPERAÇÃO: CADASTRAR ---
if ($acao === 'cadastrar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente_id      = $_POST['cliente_id'];
    $nome            = trim($_POST['nome']);
    $especie         = trim($_POST['especie']);
    $raca            = trim($_POST['raca']) ?: 'SRD';
    $data_nasc       = $_POST['data_nascimento'] ?: null;
    $comportamento   = $_POST['comportamento'];
    $nome_foto_final = 'default_pet.png';

    // Processamento do Upload da Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $pasta_destino = '../assets/uploads/';
        
        // Se a pasta não existir fisicamente, o PHP cria automaticamente
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }

        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        // Renomeia o arquivo com um ID único para nunca sobrescrever fotos com o mesmo nome
        $nome_foto_final = uniqid('pet_', true) . '.' . $extensao;

        move_uploaded_file($_FILES['foto']['tmp_name'], $pasta_destino . $nome_foto_final);
    }

    try {
        $sql = "INSERT INTO pets (cliente_id, nome, especie, raca, data_nascimento, comportamento, foto) 
                VALUES (:cliente_id, :nome, :especie, :raca, :data_nasc, :comportamento, :foto)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':cliente_id'    => $cliente_id,
            ':nome'          => $nome,
            ':especie'       => $especie,
            ':raca'          => $raca,
            ':data_nasc'     => $data_nasc,
            ':comportamento' => $comportamento,
            ':foto'          => $nome_foto_final
        ]);

        // Cria log de auditoria
        $log = $pdo->prepare("INSERT INTO log_atividades (usuario_id, acao) VALUES (:uid, :acao)");
        $log->execute([
            ':uid'  => $_SESSION['usuario_id'],
            ':acao' => "Cadastrou o pet '$nome' ($especie) para o tutor de ID $cliente_id."
        ]);

        header("Location: ../views/pets.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao cadastrar pet: " . $e->getMessage());
    }
}

// --- OPERAÇÃO: DELETAR ---
if ($acao === 'deletar') {
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pets WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../views/pets.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao deletar pet: " . $e->getMessage());
    }
}