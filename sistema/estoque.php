<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$alerta = '';
$sucesso = '';

// Processar Movimentação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produto_id = (int)$_POST['produto_id'];
    $tipo = $_POST['tipo'];
    $quantidade = (int)$_POST['quantidade'];
    $data = $_POST['data'];
    $usuario_id = $_SESSION['usuario_id'];

    if ($quantidade <= 0 || empty($data)) {
        $alerta = "Informe uma quantidade válida e a data da movimentação.";
    } else {
        // Buscar produto atual
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $prod = $stmt->fetch();

        if ($prod) {
            $novaQtd = $prod['quantidade_estoque'];
            if ($tipo === 'entrada') {
                $novaQtd += $quantidade;
            } else { // saída
                $novaQtd -= $quantidade;
            }

            if ($novaQtd < 0) {
                $alerta = "Erro: A quantidade em estoque não pode ficar negativa!";
            } else {
                // Atualiza estoque
                $up = $pdo->prepare("UPDATE produtos SET quantidade_estoque = ? WHERE id = ?");
                $up->execute([$novaQtd, $produto_id]);

                // Registra movimentação (Histórico / Rastreabilidade)
                $log = $pdo->prepare("INSERT INTO movimentacoes (produto_id, usuario_id, tipo, quantidade, data_movimentacao) VALUES (?, ?, ?, ?, ?)");
                $log->execute([$produto_id, $usuario_id, $tipo, $quantidade, $data]);

                $sucesso = "Movimentação registrada com sucesso!";

                // Requisito 7.1.4 / Requisito Mínimo: Alerta Automático na Saída
                if ($tipo === 'saida' && $novaQtd < $prod['estoque_minimo']) {
                    $alerta = "⚠️ ATENÇÃO: O produto '{$prod['nome']}' ficou abaixo do estoque mínimo! (Estoque Atual: {$novaQtd} | Mínimo: {$prod['estoque_minimo']})";
                }
            }
        }
    }
}

// 7.1.1: Buscar produtos e ordenar alfabeticamente usando um algoritmo de ordenação (Bubble Sort)
$produtos = $pdo->query("SELECT * FROM produtos")->fetchAll();

function bubbleSortProdutos(&$arr) {
    $n = count($arr);
    for ($i = 0; $i < $n - 1; $i++) {
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if (strcasecmp($arr[$j]['nome'], $arr[$j + 1]['nome']) > 0) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;
            }
        }
    }
}
bubbleSortProdutos($produtos);

// Histórico de movimentações
$historico = $pdo->query("
    SELECT m.*, p.nome as produto_nome, u.nome as usuario_nome 
    FROM movimentacoes m
    JOIN produtos p ON m.produto_id = p.id
    JOIN usuarios u ON m.usuario_id = u.id
    ORDER BY m.data_movimentacao DESC
")->fetchAll();
?>

<div class="nav-menu">
    <a href="dashboard.php">← Voltar ao Menu Principal</a>
</div>

<h2>Gestão e Movimentação de Estoque</h2>

<?php if ($sucesso): ?><div class="alert alert-success"><?= $sucesso ?></div><?php endif; ?>
<?php if ($alerta): ?><div class="alert alert-warning"><?= $alerta ?></div><?php endif; ?>

<!-- Formulário de Movimentação -->
<form method="POST" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 25px;">
    <h3>Lançar Movimentação</h3>
    
    <label>Selecione o Produto (Ordenado Alfabeticamente):</label>
    <select name="produto_id" required>
        <?php foreach ($produtos as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['nome']) ?> (Estoque Atual: <?= $p['quantidade_estoque'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Tipo de Movimentação:</label>
    <select name="tipo" required>
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
    </select>

    <label>Quantidade:</label>
    <input type="number" name="quantidade" min="1" required>

    <label>Data e Hora da Operação:</label>
    <input type="datetime-local" name="data" value="<?= date('Y-m-d\TH:i') ?>" required>

    <button type="submit">Registrar Movimentação</button>
</form>

<h3>Histórico de Movimentações (Rastreabilidade)</h3>
<table>
    <thead>
        <tr>
            <th>Data/Hora</th>
            <th>Produto</th>
            <th>Tipo</th>
            <th>Qtd.</th>
            <th>Responsável</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($historico) > 0): ?>
            <?php foreach ($historico as $h): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($h['data_movimentacao'])) ?></td>
                    <td><?= htmlspecialchars($h['produto_nome']) ?></td>
                    <td><strong><?= strtoupper($h['tipo']) ?></strong></td>
                    <td><?= $h['quantidade'] ?></td>
                    <td><?= htmlspecialchars($h['usuario_nome']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Nenhuma movimentação registrada.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once 'includes/footer.php'; ?>