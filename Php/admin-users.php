<?php
date_default_timezone_set('America/Sao_Paulo');

// Função para carregar todos os usuários
function carregarTodosUsuarios() {
    $clientes_file = 'clientes.txt';
    $usuarios = [];
    if (file_exists($clientes_file)) {
        $linhas = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($linhas as $linha) {
            $dados = explode(',', $linha);
            // Certifique-se de que a linha tem dados suficientes para evitar erros
            if (count($dados) >= 2) {
                $usuarios[] = [
                    'email' => $dados[0],
                    'nickname' => $dados[1],
                    'saldo_moedas' => isset($dados[3]) ? (int)$dados[3] : 0,
                    'itens_comprados_count' => isset($dados[4]) && $dados[4] !== '' ? count(explode(';', $dados[4])) : 0,
                    'amigos_count' => isset($dados[5]) && $dados[5] !== '' ? count(explode(';', $dados[5])) : 0
                ];
            }
        }
    }
    return $usuarios;
}

$lista_usuarios = carregarTodosUsuarios();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Gerenciar Usuários</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Reutiliza o CSS principal -->
    <link rel="stylesheet" href="../css/admin.css"> <!-- CSS específico para o painel admin -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos adicionais para a tabela de usuários */
        .users-table-container {
            background-color: var(--card-background);
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            overflow-x: auto;
            margin-top: 40px;
        }

        .users-table-container h3 {
            font-size: 1.5em;
            color: #00e676;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .users-table-container table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-color-light);
            min-width: 700px; /* Garante largura mínima para a tabela */
        }

        .users-table-container th,
        .users-table-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #3a3a3a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .users-table-container th {
            background-color: #3a3a3a;
            font-weight: bold;
            color: #ccc;
        }

        .users-table-container tbody tr:hover {
            background-color: #333;
        }

        .users-table-container .action-buttons {
            display: flex;
            gap: 5px;
        }

        .users-table-container .action-buttons .btn {
            padding: 6px 10px;
            font-size: 0.8em;
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
            <h1 class="admin-page-title"><i class="fas fa-users"></i> Gerenciar Usuários</h1>

            <section class="users-table-container">
                <h3><i class="fas fa-list"></i> Lista de Usuários Cadastrados</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Nickname</th>
                            <th>Saldo Moedas</th>
                            <th>Itens Comprados</th>
                            <th>Amigos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lista_usuarios)): ?>
                            <?php foreach ($lista_usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['email']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nickname']) ?></td>
                                    <td><?= number_format($usuario['saldo_moedas'], 0, ',', '.') ?></td>
                                    <td><?= $usuario['itens_comprados_count'] ?></td>
                                    <td><?= $usuario['amigos_count'] ?></td>
                                    <td class="action-buttons">
                                        <button class="btn btn-primary"><i class="fas fa-eye"></i> Ver</button>
                                        <button class="btn btn-danger"><i class="fas fa-ban"></i> Banir</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Nenhum usuário cadastrado ainda.</td>
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
    </script>
</body>
</html>
