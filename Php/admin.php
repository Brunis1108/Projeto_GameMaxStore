<?php
date_default_timezone_set('America/Sao_Paulo');

// Inicializa arrays para armazenar os dados processados
$compras = [];
$total_receita = 0;
$total_vendas = 0;
$vendas_por_pacote = [];
$vendas_por_categoria = [
    'Skins' => 0,
    'Pacotes' => 0,
    'Armas' => 0,
    'Emotes' => 0,
    'Boosts' => 0 // Adicione outras categorias conforme necessário
];
$vendas_diarias = []; // Para o gráfico de vendas diárias

// Caminho para o arquivo de compras
$caminho_compras = 'Banco/compras.txt';

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
            $mes_atual = date('Y-m'); // Formato Ano-Mês para filtrar por mês

            // Processa apenas compras do mês atual para "Receita Total (Mês)"
            if (date('Y-m', strtotime($dataHora)) === $mes_atual) {
                $total_receita += $valor;
                $total_vendas++;
            }

            // Agrega vendas por pacote
            if (!isset($vendas_por_pacote[$pacote])) {
                $vendas_por_pacote[$pacote] = ['vendas' => 0, 'receita' => 0];
            }
            $vendas_por_pacote[$pacote]['vendas']++;
            $vendas_por_pacote[$pacote]['receita'] += $valor;

            // Agrega vendas por categoria (simplificado, você pode precisar de uma lógica mais robusta)
            // Exemplo: se o nome do pacote contém "Skin", "Pacote", "Arma", etc.
            if (stripos($pacote, 'Skin') !== false) {
                $vendas_por_categoria['Skins'] += $valor;
            } elseif (stripos($pacote, 'Pacote') !== false) {
                $vendas_por_categoria['Pacotes'] += $valor;
            } elseif (stripos($pacote, 'Arma') !== false) {
                $vendas_por_categoria['Armas'] += $valor;
            } elseif (stripos($pacote, 'Emote') !== false) {
                $vendas_por_categoria['Emotes'] += $valor;
            } elseif (stripos($pacote, 'Boost') !== false) {
                $vendas_por_categoria['Boosts'] += $valor;
            } else {
                // Categoria padrão ou "Outros"
                if (!isset($vendas_por_categoria['Outros'])) {
                    $vendas_por_categoria['Outros'] = 0;
                }
                $vendas_por_categoria['Outros'] += $valor;
            }

            // Agrega vendas diárias para o gráfico
            if (!isset($vendas_diarias[$data])) {
                $vendas_diarias[$data] = 0;
            }
            $vendas_diarias[$data]++; // Contagem de vendas por dia
        }
    }
}

// Ordena os pacotes mais vendidos
uasort($vendas_por_pacote, function($a, $b) {
    return $b['vendas'] <=> $a['vendas'];
});
$top_5_pacotes = array_slice($vendas_por_pacote, 0, 5, true);

// Calcula o ticket médio
$ticket_medio = $total_vendas > 0 ? $total_receita / $total_vendas : 0;

// Prepara dados para o gráfico de vendas diárias (últimos 7 dias)
$ultimos_7_dias = [];
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d', strtotime("-$i days"));
    $ultimos_7_dias[$data] = $vendas_diarias[$data] ?? 0;
}
$labels_vendas_diarias = json_encode(array_keys($ultimos_7_dias));
$data_vendas_diarias = json_encode(array_values($ultimos_7_dias));

// Prepara dados para o gráfico de receita por categoria
$labels_receita_categoria = json_encode(array_keys($vendas_por_categoria));
$data_receita_categoria = json_encode(array_values($vendas_por_categoria));

// Dados para itens com menor desempenho (simplificado, você pode precisar de mais lógica)
// Aqui, vamos considerar os pacotes que não estão no top 5 e que tiveram poucas vendas
$itens_menor_desempenho = [];
foreach ($vendas_por_pacote as $pacote_nome => $dados) {
    if (!array_key_exists($pacote_nome, $top_5_pacotes) && $dados['vendas'] < 10) { // Exemplo: menos de 10 vendas
        $itens_menor_desempenho[$pacote_nome] = $dados;
    }
}
// Ordena por vendas (crescente) para mostrar os de menor desempenho primeiro
uasort($itens_menor_desempenho, function($a, $b) {
    return $a['vendas'] <=> $b['vendas'];
});
$top_5_menor_desempenho = array_slice($itens_menor_desempenho, 0, 5, true);

// --- INÍCIO DA NOVA LÓGICA PARA USUÁRIOS ---
$caminho_clientes = 'clientes.txt';
$total_usuarios = 0;
$ultimos_usuarios_cadastrados = []; // Para a tabela de últimos usuários

if (file_exists($caminho_clientes)) {
    $linhas_clientes = file($caminho_clientes, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $total_usuarios = count($linhas_clientes);

    // Coleta os últimos 5 usuários cadastrados (assumindo que são adicionados ao final do arquivo)
    $ultimos_5_clientes = array_slice($linhas_clientes, -5);
    foreach ($ultimos_5_clientes as $linha_cliente) {
        $dados_cliente = explode(',', $linha_cliente);
        if (count($dados_cliente) >= 2) { // Garante que há pelo menos email e nickname
            $ultimos_usuarios_cadastrados[] = [
                'email' => htmlspecialchars($dados_cliente[0]),
                'nickname' => htmlspecialchars($dados_cliente[1])
            ];
        }
    }
    // Inverte a ordem para mostrar o mais recente primeiro
    $ultimos_usuarios_cadastrados = array_reverse($ultimos_usuarios_cadastrados);
}

$novos_usuarios = $total_usuarios; // Atualiza a variável para o dashboard
// --- FIM DA NOVA LÓGICA PARA USUÁRIOS ---
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Painel Administrativo</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Reutiliza o CSS principal -->
    <link rel="stylesheet" href="../css/admin.css"> <!-- CSS específico para o painel admin -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Biblioteca de Gráficos (ex: Chart.js) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .summary-card .metric-value {
    font-size: 30px;
}
</style>
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
                    <li><a href="admin-users.php"><i class="fas fa-users"></i> Usuários</a></li>
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
            <h1 class="admin-page-title"><i class="fas fa-tachometer-alt"></i> Dashboard Administrativa</h1>

            <section class="admin-summary-cards">
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="card-content">
                        <h3>Receita Total (Mês)</h3>
                        <p class="metric-value">R$ <?= number_format($total_receita, 2, ',', '.') ?></p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> Dados do Mês Atual</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="card-content">
                        <h3>Vendas Realizadas (Mês)</h3>
                        <p class="metric-value"><?= $total_vendas ?></p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> Dados do Mês Atual</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <h3>Total de Usuários</h3>
                        <p class="metric-value"><?= $total_usuarios ?></p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> Usuários Cadastrados</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="card-content">
                        <h3>Ticket Médio</h3>
                        <p class="metric-value">R$ <?= number_format($ticket_medio, 2, ',', '.') ?></p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> Mês Atual</span>
                    </div>
                </div>
            </section>

            <section class="admin-charts-section">
                <div class="chart-card">
                    <h3>Vendas Diárias (Últimos 7 Dias)</h3>
                    <canvas id="dailySalesChart"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Receita por Categoria</h3>
                    <canvas id="revenueByCategoryChart"></canvas>
                </div>
            </section>

            <section class="admin-data-tables">
                <div class="data-table-card">
                    <h3><i class="fas fa-trophy"></i> Top 5 Pacotes Mais Vendidos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pacote</th>
                                <th>Vendas</th>
                                <th>Receita</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($top_5_pacotes as $pacote_nome => $dados): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($pacote_nome) ?></td>
                                    <td><?= $dados['vendas'] ?></td>
                                    <td>R$ <?= number_format($dados['receita'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($top_5_pacotes)): ?>
                                <tr><td colspan="4">Nenhum pacote vendido ainda.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="data-table-card">
                    <h3><i class="fas fa-user-plus"></i> Últimos Usuários Cadastrados</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nickname</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($ultimos_usuarios_cadastrados)): ?>
                                <?php foreach ($ultimos_usuarios_cadastrados as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['nickname'] ?></td>
                                        <td><?= $usuario['email'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2">Nenhum usuário cadastrado ainda.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="admin-quick-actions">
                <h2><i class="fas fa-bolt"></i> Ações Rápidas</h2>
                <div class="action-grid">
                    <a href="#" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Adicionar Novo Produto</span>
                    </a>
                    <a href="#" class="action-card">
                        <i class="fas fa-gift"></i>
                        <span>Criar Nova Promoção</span>
                    </a>
                    <a href="admin-users.php" class="action-card">
                        <i class="fas fa-user-slash"></i>
                        <span>Gerenciar Usuários</span>
                    </a>
                    <a href="admin-reports.php" class="action-card">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>Ver Relatórios Financeiros</span>
                    </a>
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

        // Dados para os gráficos (reais do PHP)
        const dailySalesData = {
            labels: <?= $labels_vendas_diarias ?>,
            datasets: [{
                label: 'Vendas Diárias',
                data: <?= $data_vendas_diarias ?>,
                backgroundColor: 'rgba(0, 123, 255, 0.6)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        const revenueByCategoryData = {
            labels: <?= $labels_receita_categoria ?>,
            datasets: [{
                label: 'Receita por Categoria',
                data: <?= $data_receita_categoria ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(201, 203, 207, 0.7)' // Cor para 'Outros'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(201, 203, 207, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Renderizar gráficos
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'bar',
            data: dailySalesData,
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
                            color: 'var(--text-color-light)'
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
                        display: false
                    }
                }
            }
        });

        const revenueByCategoryCtx = document.getElementById('revenueByCategoryChart').getContext('2d');
        new Chart(revenueByCategoryCtx, {
            type: 'pie',
            data: revenueByCategoryData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: 'var(--text-color-light)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
