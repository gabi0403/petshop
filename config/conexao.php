<?php
// Configurações do servidor local do PostgreSQL
$host     = 'localhost';
$port     = '5432';
$dbname   = 'petshop_php'; 
$user     = 'postgres'; 
$password = 'postgres'; 

try {
    // String de conexão para o driver do PostgreSQL (pgsql)
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    
    // Cria a instância do PDO
    $pdo = new PDO($dsn, $user, $password);
    
    // Configura o PDO para lançar exceções em caso de erros de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura o retorno padrão das consultas como Arrays Associativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Se a conexão falhar, o sistema exibe o erro e para a execução
    die("Erro crítico de conexão com o banco de dados: " . $e->getMessage());
}