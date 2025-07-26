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
            <h1 class="admin-page-title"><i class="fas fa-tachometer-alt"></i> Dashboard Administrativa</h1>

            <section class="admin-summary-cards">
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="card-content">
                        <h3>Receita Total (Mês)</h3>
                        <p class="metric-value">R$ 125.450,00</p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> +12% vs. Mês Anterior</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="card-content">
                        <h3>Vendas Realizadas</h3>
                        <p class="metric-value">2.876</p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> +8% vs. Mês Anterior</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <h3>Novos Usuários</h3>
                        <p class="metric-value">345</p>
                        <span class="metric-change positive"><i class="fas fa-arrow-up"></i> +5% vs. Mês Anterior</span>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="card-content">
                        <h3>Ticket Médio</h3>
                        <p class="metric-value">R$ 43,62</p>
                        <span class="metric-change negative"><i class="fas fa-arrow-down"></i> -1% vs. Mês Anterior</span>
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
                            <tr><td>1</td><td>Pacote Lendário</td><td>520</td><td>R$ 54.600</td></tr>
                            <tr><td>2</td><td>Skin Exclusiva</td><td>480</td><td>R$ 30.720</td></tr>
                            <tr><td>3</td><td>Kit Inicial Pro</td><td>350</td><td>R$ 14.875</td></tr>
                            <tr><td>4</td><td>Arma Épica</td><td>290</td><td>R$ 34.800</td></tr>
                            <tr><td>5</td><td>Emote Raro</td><td>210</td><td>R$ 6.300</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="data-table-card">
                    <h3><i class="fas fa-frown"></i> Itens com Menor Desempenho</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Vendas (Mês)</th>
                                <th>Última Venda</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Pacote de Boost Básico</td><td>5</td><td>Há 25 dias</td></tr>
                            <tr><td>2</td><td>Acessório de Cabeça Raro</td><td>3</td><td>Há 40 dias</td></tr>
                            <tr><td>3</td><td>Emblema de Clã</td><td>1</td><td>Há 60 dias</td></tr>
                            <tr><td>4</td><td>Poção de XP (Pequena)</td><td>0</td><td>Nunca</td></tr>
                            <tr><td>5</td><td>Caixa Misteriosa Antiga</td><td>0</td><td>Nunca</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="admin-quick-actions">
                <h2><i class="fas fa-bolt"></i> Ações Rápidas</h2>
                <div class="action-grid">
                    <a href="admin-products.html?action=add" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Adicionar Novo Produto</span>
                    </a>
                    <a href="admin-promotions.html?action=create" class="action-card">
                        <i class="fas fa-gift"></i>
                        <span>Criar Nova Promoção</span>
                    </a>
                    <a href="admin-users.html?action=ban" class="action-card">
                        <i class="fas fa-user-slash"></i>
                        <span>Gerenciar Usuários</span>
                    </a>
                    <a href="admin-reports.html?type=finance" class="action-card">
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

        // Dados para os gráficos (exemplo)
        const dailySalesData = {
            labels: ['Dia 1', 'Dia 2', 'Dia 3', 'Dia 4', 'Dia 5', 'Dia 6', 'Dia 7'],
            datasets: [{
                label: 'Vendas Diárias',
                data: [120, 190, 150, 230, 180, 250, 210],
                backgroundColor: 'rgba(0, 123, 255, 0.6)',
                borderColor: 'rgba(0, 123, 255, 1)',
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        const revenueByCategoryData = {
            labels: ['Skins', 'Pacotes', 'Armas', 'Emotes', 'Boosts'],
            datasets: [{
                label: 'Receita por Categoria',
                data: [40000, 35000, 25000, 10000, 5000],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
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
