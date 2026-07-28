<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestão de Estoque - SAEP</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>SAEP - Gestão de Almoxarifado</h1>
        <div>
            <span>Usuário: <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span> | 
            <a href="logout.php" class="btn-logout">Sair</a>
        </div>
    </header>
    <div class="container">