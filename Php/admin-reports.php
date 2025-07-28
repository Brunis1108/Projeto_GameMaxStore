<?php
date_default_timezone_set('America/Sao_Paulo');

// Inicializa arrays
$compras = [];
$fluxo_por_dia = [];

// Caminho para o arquivo de compras
$caminho_compras = 'Banco/compras.txt'; // Ajustado para o caminho correto

if (file_exists($caminho_compras)) {
    $linhas = file($caminho_compras, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        // Novo padrão de leitura para o formato atual
        // Exemplo de linha: [2025-07-27 14:04:18] Pacote: Pacote Lendário Hall Of Legends 2025 Uzi | Preço: R$ 105,00 | Email: Não informado
        preg_match('/\[(.*?)\]\s*Pacote:\s*(.*?)\s*\|\s*Preço:\s*R\$\s*([\d,\.]+)\s*\|\s*Email:\s*(.*)/', $linha, $matches);

        if (count($matches) === 5) {
            $dataHora = trim($matches[1]);
            $pacote = trim($matches[2]);
            $valorStr = trim($matches[3]);
            $email = trim($matches[4]);

            // Converte para float (cuidado com vírgula para ponto decimal)
            $valor = floatval(str_replace(',', '.', $valorStr));
            $data = date('Y-m-d', strtotime($dataHora));

            // Armazena no array de compras
            $compras[] = compact('dataHora', 'pacote', 'valor', 'email');

            // Soma por dia
            if (!isset($fluxo_por_dia[$data])) {
                $fluxo_por_dia[$data] = 0;
            }
            $fluxo_por_dia[$data] += $valor;
        }
    }
}

// Ordena o fluxo por dia para o gráfico
ksort($fluxo_por_dia);

// Prepara os dados para o Chart.js
$labels_fluxo = json_encode(array_keys($fluxo_por_dia));
$data_fluxo = json_encode(array_values($fluxo_por_dia));
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Relatórios Financeiros</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Reutiliza o CSS principal -->
    <link rel="stylesheet" href="../css/admin.css"> <!-- CSS específico para o painel admin -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos adicionais para a tabela de vendas */
        .sales-table-container {
            background-color: var(--card-background);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            overflow-x: auto;
            margin-top: 40px;
        }

        .sales-table-container h3 {
            font-size: 1.5em;
            color: #00e676;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sales-table-container table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-color-light);
            min-width: 600px;
            /* Garante largura mínima para a tabela */
        }

        .sales-table-container th,
        .sales-table-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #3a3a3a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }

        .sales-table-container th {
            background-color: #3a3a3a;
            font-weight: bold;
            color: #ccc;
        }

        .sales-table-container tbody tr:hover {
            background-color: #333;
        }

        .chart-card {
            color: white;
            /* Ou ajuste o valor conforme necessário */
            padding-bottom: 50px;
            /* Alternativa ou adicional */
        }
    </style>
</head>

<body>
    <header class="main-header admin-header">
        <div class="container">
            <div class="logo">
                <a href="admin.php">GameMaxAdmin</a>
            </div>
            <nav class="main-nav admin-nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-box"></i> Produtos</a></li>
                    <li><a href="#"><i class="fas fa-tags"></i> Promoções</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Usuários</a></li>
                    <li><a href="admin-reports.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Configurações</a></li>
                </ul>
            </nav>
            <div class="user-actions admin-actions">
                <span class="admin-name"><i class="fas fa-user-shield"></i> Olá, Administrador!</span>
                <a href="login.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Sair</a>
            </div>
            <button class="menu-toggle" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main class="admin-main">
        <div class="container">
            <h1 class="admin-page-title"><i class="fas fa-chart-line"></i> Relatórios Financeiros</h1>

            <section class="admin-charts-section">
                <div class="chart-card">
                    <h3>Fluxo de Caixa por Dia</h3>
                    <canvas id="fluxoChart"></canvas>
                </div>
            </section>

            <section class="sales-table-container">
                <h3><i class="fas fa-list-alt"></i> Lista Detalhada de Vendas</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Pacote</th>
                            <th>Valor (R$)</th>
                            <th>Email do Comprador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($compras)): ?>
                            <?php foreach ($compras as $compra): ?>
                                <tr>
                                    <td><?= htmlspecialchars($compra['dataHora']) ?></td>
                                    <td><?= htmlspecialchars($compra['pacote']) ?></td>
                                    <td>R$ <?= number_format($compra['valor'], 2, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($compra['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Nenhuma venda registrada ainda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>

    <footer class="main-footer admin-footer">
        <div class="container">
            <p>&copy; 2023 GameMaxStore Admin. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // JavaScript para o menu responsivo
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });

        // Dados reais do PHP para o gráfico
        const labelsFluxo = <?= $labels_fluxo ?>;
        const dataFluxo = <?= $data_fluxo ?>;

        const ctxFluxo = document.getElementById('fluxoChart').getContext('2d');
        new Chart(ctxFluxo, {
            type: 'line',
            data: {
                labels: labelsFluxo,
                datasets: [{
                    label: 'Entradas (R$)',
                    data: dataFluxo,
                    backgroundColor: 'rgba(0, 230, 118, 0.6)', // Verde vibrante do admin.css
                    borderColor: 'rgba(0, 230, 118, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color:'#ffffff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#ffffff'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'var(--text-color-light)'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>