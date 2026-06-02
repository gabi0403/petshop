<?php
// Desativa bloqueios e importa a sua conexão
require_once '../config/conexao.php';

try {
    // 1. Limpa os usuários antigos para não dar conflito de e-mail duplicado
    $pdo->exec("DELETE FROM usuarios");

    // 2. Faz o SEU PHP gerar o hash perfeito e puro de '123456' na sua máquina
    $senha_local = password_hash('123456', PASSWORD_DEFAULT);

    // 3. Insere os dados usando o hash gerado aí dentro do seu servidor
    $sql = "INSERT INTO usuarios (nome, email, senha, telefone, tipo, cargo) VALUES
    ('Dr. Roberto Garcia', 'roberto@pethealth.com', :senha, '11999999991', 'equipe', 'Veterinário'),
    ('Camila Silva Tosadora', 'camila@pethealth.com', :senha, '11999999992', 'equipe', 'Tosador'),
    ('Carlos Tutor', 'cliente@gmail.com', :senha, '11988888888', 'cliente', 'Cliente')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':senha' => $senha_local]);

    echo "<div style='font-family: Arial, sans-serif; padding: 30px; text-align: center;'>";
    echo "<h1 style='color: #2ecc71;'>🚀 Senhas Sincronizadas com Sucesso!</h1>";
    echo "<p style='color: #7f8c8d; font-size: 18px;'>O seu próprio PHP gerou a criptografia local.</p>";
    echo "<p>Agora você já pode fechar esta aba e fazer o login.</p>";
    echo "</div>";

} catch (PDOException $e) {
    die("Erro ao rodar o script de ajuste: " . $e->getMessage());
}