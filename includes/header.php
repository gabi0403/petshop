<?php
// ============================================================================
// CABEÇALHO GLOBAL E CONTROLADOR DE MENUS - PLATAFORMA PETHEALTH & CARE
// ============================================================================
// Este arquivo monta a estrutura inicial do HTML, gerencia as sessões ativas
// e constrói a barra de navegação adaptativa baseada no nível de acesso.

// Inicia a sessão para verificar se o usuário está logado no sistema
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// [TRAVA DE SEGURANÇA BLINDADA] - Se não houver sessão ativa, chuta o invasor de volta pro login :D
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

<nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 shadow-sm border-b">
    <div class="container">
        
        <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="dashboard.php">
            <img src="../assets/css/logo.png" alt="Logo" width="35" height="35" class="d-inline-block align-text-top me-2"> 
            <p style="color: whitesmoke; margin-top: 14px; font-weight: bold;">PetHealth & Care</p>
        </a>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                
                <?php if ($_SESSION['usuario_tipo'] === 'equipe'): ?> 
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="agendamentos.php"><i class="fa-solid fa-calendar-days me-1"></i> Agendamentos</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="clientes.php"><i class="fa-solid fa-users me-1"></i> Clientes</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="pets.php"><i class="fa-solid fa-paw me-1"></i> Pets</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="mural.php"><i class="fa-solid fa-clipboard-list me-1"></i> Mural</a></li>
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="configuracoes.php"><i class="fa-solid fa-gear me-1"></i> Configurações</a></li>
                
                <?php else: ?> 
                    <li class="nav-item"><a class="nav-link text-secondary fw-medium" href="painel-tutor.php"><i class="fa-solid fa-house-user me-1"></i> Meu Painel</a></li>
                
                <?php endif; ?>
                
            </ul>
            
            <div class="d-flex align-items-center text-secondary">
                <span class="me-3 small">
                    Olá, <strong class="text-primary"><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong> (<?= htmlspecialchars($_SESSION['usuario_cargo']) ?>)
                </span>
                
                <a href="alterar-senha.php" class="btn btn-outline-secondary btn-sm me-2 rounded-pill shadow-sm">
                    <i class="fa-solid fa-key me-1"></i> Senha
                </a>
                
                <a href="../actions/logout.php" class="btn btn-outline-danger btn-sm rounded-pill shadow-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
            
        </div>
    </div>
</nav>

<div class="container">