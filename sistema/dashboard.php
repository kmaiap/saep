<?php
require_once 'includes/header.php';
?>
<h2>Painel Principal</h2>
<p>Seja bem-vindo ao sistema de controle de ferramentas e equipamentos.</p>

<div class="card-grid">
    <a href="produtos.php" class="card">
        <h3>📦 Cadastro de Produtos</h3>
        <p>Cadastre, edite, busque e remova itens do catálogo.</p>
    </a>
    <a href="estoque.php" class="card">
        <h3>🔄 Gestão de Estoque</h3>
        <p>Registre entradas/saídas e acompanhe alertas de estoque mínimo.</p>
    </a>
</div>

<?php require_once 'includes/footer.php'; ?>