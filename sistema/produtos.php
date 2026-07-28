<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$mensagem = '';
$erro = '';

// Exclusão
if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->execute([$idExcluir]);
    header("Location: produtos.php?msg=Produto excluído com sucesso!");
    exit;
}

// Inserção / Edição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $especificacoes = trim($_POST['especificacoes'] ?? '');
    $qtd = $_POST['quantidade_estoque'] ?? '';
    $min = $_POST['estoque_minimo'] ?? '';

    // Validações
    if ($nome === '' || $categoria === '' || $qtd === '' || $min === '') {
        $erro = "Por favor, preencha todos os campos obrigatórios!";
    } elseif ($qtd < 0 || $min < 0) {
        $erro = "A quantidade e o estoque mínimo não podem ser valores negativos!";
    } else {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE produtos SET nome=?, categoria=?, especificacoes=?, quantidade_estoque=?, estoque_minimo=? WHERE id=?");
            $stmt->execute([$nome, $categoria, $especificacoes, $qtd, $min, $id]);
            $mensagem = "Produto atualizado com sucesso!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO produtos (nome, categoria, especificacoes, quantidade_estoque, estoque_minimo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $categoria, $especificacoes, $qtd, $min]);
            $mensagem = "Produto cadastrado com sucesso!";
        }
    }
}

// Busca
$termo = $_GET['busca'] ?? '';
if ($termo !== '') {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE ? OR categoria LIKE ?");
    $stmt->execute(["%$termo%", "%$termo%"]);
    $produtos = $stmt->fetchAll();
} else {
    $produtos = $pdo->query("SELECT * FROM produtos")->fetchAll();
}

// Produto para Edição
$prodEditar = null;
if (isset($_GET['editar'])) {
    $idEdit = (int)$_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$idEdit]);
    $prodEditar = $stmt->fetch();
}
?>

<div class="nav-menu">
    <a href="dashboard.php">← Voltar ao Menu Principal</a>
</div>

<h2>Cadastro e Gestão de Produtos</h2>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>
<?php if ($mensagem): ?><div class="alert alert-success"><?= $mensagem ?></div><?php endif; ?>
<?php if ($erro): ?><div class="alert alert-danger"><?= $erro ?></div><?php endif; ?>

<!-- Formulário de Cadastro/Edição -->
<form method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
    <input type="hidden" name="id" value="<?= $prodEditar['id'] ?? '' ?>">
    <h3><?= $prodEditar ? 'Editar Produto' : 'Novo Produto' ?></h3>
    
    <label>Nome do Produto *:</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($prodEditar['nome'] ?? '') ?>" required>

    <label>Categoria *:</label>
    <input type="text" name="categoria" value="<?= htmlspecialchars($prodEditar['categoria'] ?? '') ?>" placeholder="Ex: Martelos, Chaves" required>

    <label>Especificações (Material, Cabo, Revestimento, etc.):</label>
    <textarea name="especificacoes"><?= htmlspecialchars($prodEditar['especificacoes'] ?? '') ?></textarea>

    <label>Quantidade Inicial em Estoque *:</label>
    <input type="number" name="quantidade_estoque" value="<?= htmlspecialchars($prodEditar['quantidade_estoque'] ?? '0') ?>" required>

    <label>Estoque Mínimo *:</label>
    <input type="number" name="estoque_minimo" value="<?= htmlspecialchars($prodEditar['estoque_minimo'] ?? '0') ?>" required>

    <button type="submit"><?= $prodEditar ? 'Atualizar Produto' : 'Cadastrar Produto' ?></button>
    <?php if ($prodEditar): ?>
        <a href="produtos.php" style="margin-left: 10px;">Cancelar Edição</a>
    <?php endif; ?>
</form>

<!-- Busca -->
<form method="GET" style="display: flex; gap: 10px; margin-bottom: 15px;">
    <input type="text" name="busca" value="<?= htmlspecialchars($termo) ?>" placeholder="Buscar produto por nome ou categoria...">
    <button type="submit" style="margin-top: 0;">Buscar</button>
</form>

<!-- Listagem em Tabela -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Especificações</th>
            <th>Qtd. Estoque</th>
            <th>Estoque Mín.</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($produtos) > 0): ?>
            <?php foreach ($produtos as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['categoria']) ?></td>
                    <td><?= htmlspecialchars($p['especificacoes']) ?></td>
                    <td><?= $p['quantidade_estoque'] ?></td>
                    <td><?= $p['estoque_minimo'] ?></td>
                    <td>
                        <a href="produtos.php?editar=<?= $p['id'] ?>">Editar</a> | 
                        <a href="produtos.php?excluir=<?= $p['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir?');" style="color: red;">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Nenhum produto encontrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'includes/footer.php'; ?>