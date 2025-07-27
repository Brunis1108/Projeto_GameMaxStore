<?php
date_default_timezone_set('America/Sao_Paulo');

// Inicializa arrays
$compras = [];
$fluxo_por_dia = [];

if (file_exists('Banco/compras.txt')) {
    $linhas = file('Banco/compras.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($linhas as $linha) {
        // Novo padrão de leitura para o formato atual
        preg_match('/\[(.*?)\]\s*Pacote:\s*(.*?)\s*\|\s*Preço:\s*R\$\s*([\d,\.]+)\s*\|\s*Email:\s*(.*)/', $linha, $matches);

        if (count($matches) === 5) {
            $dataHora = trim($matches[1]);
            $pacote = trim($matches[2]);
            $valorStr = trim($matches[3]);
            $email = trim($matches[4]);

            // Converte para float (cuidado com vírgula)
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

// Ordena o fluxo por dia para garantir que o gráfico seja exibido corretamente
ksort($fluxo_por_dia);

// Prepara os dados para o Chart.js
$labels = json_encode(array_keys($fluxo_por_dia));
$dataValues = json_encode(array_values($fluxo_por_dia));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Relatórios Financeiros</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header class="main-header admin-header">
        <div class="container">
            <div class="logo">
                <a href="admin-dashboard.html">GameMaxAdmin</a>
            </div>
            <nav class="main-nav admin-nav">
                <ul>
                    <li><a href="admin-dashboard.html"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="admin-products.html"><i class="fas fa-box"></i> Produtos</a></li>
                    <li><a href="admin-promotions.html"><i class="fas fa-tags"></i> Promoções</a></li>
                    <li><a href="admin-users.html"><i class="fas fa-users"></i> Usuários</a></li>
                    <li><a href="admin-reports.php"><i class="fas fa-chart-line"></i> Relatórios</a></li>
                    <li><a href="admin-settings.html"><i class="fas fa-cog"></i> Configurações</a></li>
                </ul>
            </nav>
            <div class="user-actions admin-actions">
                <span class="admin-name"><i class="fas fa-user-shield"></i> Olá, Administrador!</span>
                <a href="login.html" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i> Sair</a>
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
                    <h3>📊 Fluxo de Caixa por Dia</h3>
                    <canvas id="fluxoChart"></canvas>
                </div>
            </section>

            <section class="admin-data-tables">
                <div class="data-table-card">
                    <h3>📋 Lista de Vendas Recentes</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Pacote</th>
                                <th>Valor (R$)</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($compras)): ?>
                                <tr><td colspan="4" style="text-align: center;">Nenhuma venda registrada ainda.</td></tr>
                            <?php else: ?>
                                <?php foreach ($compras as $compra): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($compra['dataHora']) ?></td>
                                        <td><?= htmlspecialchars($compra['pacote']) ?></td>
                                        <td><?= number_format($compra['valor'], 2, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($compra['email']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

    <footer class="main-footer admin-footer">
        <div class="container">
            <p>&copy; 2023 GameMaxStore Admin. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // JavaScript para o menu responsivo (reutilizado)
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });

        // Dados para o gráfico de fluxo de caixa
        const fluxoData = {
            labels: <?= $labels ?>,
            datasets: [{
                label: 'Entradas (R$)',
                data: <?= $dataValues ?>,
                backgroundColor: 'rgba(0, 230, 118, 0.6)', // Verde vibrante do admin
                borderColor: 'rgba(0, 230, 118, 1)',
                borderWidth: 1,
                fill: true,
                tension: 0.3 // Adiciona uma leve curva à linha
            }]
        };

        // Renderizar gráfico de fluxo de caixa
        const ctxFluxo = document.getElementById('fluxoChart').getContext('2d');
        new Chart(ctxFluxo, {
            type: 'line', // Alterado para gráfico de linha para melhor visualização de tendências
            data: fluxoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)' // Linhas de grade mais claras
                        },
                        ticks: {
                            color: 'var(--text-color-light)', // Cor do texto dos ticks
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: 'var(--text-color-light)'
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
                                    label += 'R$ ' + context.parsed.y.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
