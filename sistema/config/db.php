<?php
$host = 'localhost';
$port = '5432'; // Porta padrão do PostgreSQL
$db   = 'saep_db';
$user = 'postgres';
$pass = 'postgres'; 

try {
    // String de conexão DSN corrigida
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erro na conexão com o PostgreSQL: " . $e->getMessage());
}