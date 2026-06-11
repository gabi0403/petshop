<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHealth & Care - Sistema de Gestão Concierge</title>
    
    <!-- Chamadas do Bootstrap 5 e fontes de ícones do FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
            height: 100vh;
            color: #334155; 
            display: flex;
            align-items: center;
        }
        .hero-section {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 24px;
            backdrop-filter: blur(12px);
        }

        .btn-principal {
            background-color: #2c3e50; 
            color: #ffffff !important;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-principal:hover {
            background-color: #1a252f; 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        
        <div class="col-md-9 text-center hero-section p-5 shadow-lg">
            <div class="mb-4">
                <img src="../assets/css/logo.png" alt="Logo PetHealth" width="100" height="100" class="d-inline-block align-text-top img-fluid">
            </div>
            <h1 class="display-4 fw-bold mb-2 text-dark">PetHealth & Care</h1>
            <p class="lead mb-4 fs-4 fw-medium" style="color: #0284c7;">
                Plataforma de Gestão Integrada de Alto Nível & Acompanhamento em Tempo Real
            </p>
            <hr class="my-4" style="border-color: rgba(0, 0, 0, 0.1)">
            
            <p class="mb-5 px-md-5 text-secondary" style="font-size: 1.05rem; line-height: 1.6;">
                Um ecossistema completo projetado para clínicas veterinárias e petshops modernos. 
                Ofereça transparência total aos tutores através da nossa esteira operacional ao vivo.
            </p>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="views/login.php" class="btn btn-principal btn-lg px-5 py-3 fw-bold rounded-pill shadow">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Acessar o Sistema
                </a>
            </div>
            
        </div>
    </div>
</div>

</body>
</html>