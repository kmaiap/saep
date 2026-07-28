<?php
session_start();
require_once 'config/db.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Verificação simples (ou password_verify se usado hash)
        if ($usuario && $usuario['senha'] === $senha) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = "E-mail ou senha incorretos. Verifique suas credenciais.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - SAEP Almoxarifado</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-box">
        <h2>Acesso ao Sistema</h2>
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>E-mail:</label>
            <input type="email" name="email" required placeholder="Digite seu e-mail">
            
            <label>Senha:</label>
            <input type="password" name="senha" required placeholder="Digite sua senha">
            
            <button type="submit" style="width: 100%;">Entrar</button>
        </form>
    </div>
</body>
</html>