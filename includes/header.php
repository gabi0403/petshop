<?php
// Inicia a sessão para verificar se o usuário está logado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Trava de segurança: Se não houver a sessão do usuário, chuta de volta pro login :D
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php?erro=sem_acesso");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - PetHealth & Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fa-solid fa-heart-pulse text-danger me-2"></i>PetHealth & Care
        </a>
        <button class="navbar-collapse collapse" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($_SESSION['usuario_tipo'] === 'equipe'): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="agendamentos.php"><i class="fa-solid fa-calendar-days me-1"></i> Agendamentos</a></li>
                    <li class="nav-item"><a class="nav-link" href="clientes.php"><i class="fa-solid fa-users me-1"></i> Clientes</a></li>
                    <li class="nav-item"><a class="nav-link" href="pets.php"><i class="fa-solid fa-paw me-1"></i> Pets</a></li>
                    <li class="nav-item"><a class="nav-link" href="mural.php"><i class="fa-solid fa-clipboard-list me-1"></i> Mural</a></li>
                    <li class="nav-item"><a class="nav-link" href="configuracoes.php"><i class="fa-solid fa-gear me-2"></i> Configurações</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="painel-tutor.php"><i class="fa-solid fa-house-user me-1"></i> Meu Painel</a></li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex align-items-center text-white">
                <span class="me-3 small">Olá, <strong class="text-info"><?= $_SESSION['usuario_nome'] ?></strong> (<?= $_SESSION['usuario_cargo'] ?>)</span>
                <a href="../actions/logout.php" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            </div>
        </div>
    </div>
</nav>

<div class="container">