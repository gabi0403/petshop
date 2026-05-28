<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PetHealth & Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        .btn-custom {
            background-color: #2c3e50;
            color: #fff;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background-color: #1a252f;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-login p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-secondary">PetHealth & Care</h2>
                        <p class="text-muted">Plataforma de Gestão e Cuidados Pet</p>
                    </div>

                    <?php if (isset($_GET['erro'])): ?>
                        <div class="alert alert-danger text-center py-2" role="alert">
                            <?php 
                                if($_GET['erro'] == 'dados_invalidos') echo "E-mail ou senha incorretos.";
                                if($_GET['erro'] == 'campos_vazios') echo "Preencha todos os campos.";
                                if($_GET['erro'] == 'sem_acesso') echo "Faça login para acessar o sistema.";
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="../actions/login-action.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label text-secondary fw-semibold">E-mail Corporativo / Tutor</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="seuemail@exemplo.com" required>
                        </div>
                        <div class="mb-4">
                            <label for="senha" class="form-label text-secondary fw-semibold">Senha de Acesso</label>
                            <input type="password" class="form-control" id="senha" name="senha" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100 py-2 fw-semibold">Entrar no Sistema</button>
                    </form>

                </div>
            </div>
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none text-muted small">← Voltar para a Página Inicial</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>