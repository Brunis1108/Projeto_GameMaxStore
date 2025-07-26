<?php
date_default_timezone_set('America/Sao_Paulo');
$compras = [];
$dre = [
    'Receita Bruta' => 0,
    'Descontos' => 0,
    'Custos' => 0,
    'Lucro Bruto' => 0,
    'Despesas' => 0,
    'Lucro Líquido' => 0
];
$fluxo_por_dia = [];

if (file_exists('compras.txt')) {
    $linhas = file('compras.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        preg_match_all('/email:\s*(.*?)\s*\|\s*pacote:\s*(.*?)\s*\|\s*valor:\s*([\d.]+)\s*\|\s*data:\s*(.*)/', $linha, $matches);
        if ($matches && count($matches[0]) > 0) {
            $email = $matches[1][0];
            $pacote = $matches[2][0];
            $valor = floatval($matches[3][0]);
            $data = $matches[4][0];

            $compras[] = compact('email', 'pacote', 'valor', 'data');
            $dre['Receita Bruta'] += $valor;

            $dia = date('Y-m-d', strtotime($data));
            if (!isset($fluxo_por_dia[$dia])) $fluxo_por_dia[$dia] = 0;
            $fluxo_por_dia[$dia] += $valor;
        }
    }
}

// Simulações
$dre['Custos'] = $dre['Receita Bruta'] * 0.3;
$dre['Despesas'] = $dre['Receita Bruta'] * 0.2;
$dre['Lucro Bruto'] = $dre['Receita Bruta'] - $dre['Custos'];
$dre['Lucro Líquido'] = $dre['Lucro Bruto'] - $dre['Despesas'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatórios Financeiros | GameMaxAdmin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <main class="admin-main">
        <div class="container">
            <h1>📈 Relatórios Financeiros</h1>
            <div class="report-buttons">
                <button onclick="mostrarRelatorio('dre')">📊 DRE</button>
                <button onclick="mostrarRelatorio('fluxo')">📈 Fluxo de Caixa</button>
            </div>

            <!-- DRE -->
            <div id="relatorio-dre" style="display: none;">
                <h2>DRE - Resultado do Exercício</h2>
                <canvas id="dreChart"></canvas>
                <table border="1" cellpadding="5" style="margin-top:20px; width:100%;">
                    <tr><th>Categoria</th><th>Valor (R$)</th></tr>
                    <?php foreach ($dre as $categoria => $valor): ?>
                        <tr>
                            <td><?= $categoria ?></td>
                            <td><?= number_format($valor, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- Fluxo de Caixa -->
            <div id="relatorio-fluxo" style="display: none;">
                <h2>Fluxo de Caixa</h2>
                <canvas id="fluxoChart"></canvas>
                <table border="1" cellpadding="5" style="margin-top:20px; width:100%;">
                    <tr><th>Data</th><th>Valor</th></tr>
                    <?php foreach ($fluxo_por_dia as $dia => $valor): ?>
                        <tr>
                            <td><?= $dia ?></td>
                            <td>R$ <?= number_format($valor, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </main>

    <script>
        function mostrarRelatorio(tipo) {
            document.getElementById('relatorio-dre').style.display = (tipo === 'dre') ? 'block' : 'none';
            document.getElementById('relatorio-fluxo').style.display = (tipo === 'fluxo') ? 'block' : 'none';
        }

        // DRE Chart
        new Chart(document.getElementById('dreChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($dre)) ?>,
                datasets: [{
                    label: 'Valores em R$',
                    data: <?= json_encode(array_values($dre)) ?>,
                    backgroundColor: ['#3498db', '#e74c3c', '#f1c40f', '#1abc9c', '#9b59b6', '#2ecc71']
                }]
            },
            options: { responsive: true }
        });

        // Fluxo de Caixa Chart
        new Chart(document.getElementById('fluxoChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: <?= json_encode(array_keys($fluxo_por_dia)) ?>,
                datasets: [{
                    label: 'Entradas por Dia (R$)',
                    data: <?= json_encode(array_values($fluxo_por_dia)) ?>,
                    borderColor: '#2ecc71',
                    backgroundColor: 'rgba(46,204,113,0.2)',
                    fill: true
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>
