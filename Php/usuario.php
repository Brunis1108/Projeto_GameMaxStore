<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Função para carregar dados do usuário
function carregarDadosUsuario($email) {
    $clientes_file = 'clientes.txt';
    if (file_exists($clientes_file)) {
        $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($clientes as $cliente) {
            $dados = explode(',', $cliente);
            if ($dados[0] === $email) {
                return [
                    'email' => $dados[0],
                    'nickname' => $dados[1],
                    'senha_hash' => $dados[2],
                    'saldo_moedas' => isset($dados[3]) ? (int)$dados[3] : 0,
                    'itens_comprados' => isset($dados[4]) && $dados[4] !== '' ? explode(';', $dados[4]) : [],
                    'amigos' => isset($dados[5]) && $dados[5] !== '' ? explode(';', $dados[5]) : [],
                    'solicitacoes_amizade' => isset($dados[6]) && $dados[6] !== '' ? explode(';', $dados[6]) : []
                ];
            }
        }
    }
    return null;
}

// Função para salvar dados do usuário
function salvarDadosUsuario($usuario) {
    $clientes_file = 'clientes.txt';
    $linhas = [];
    $encontrado = false;

    if (file_exists($clientes_file)) {
        $linhas = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    foreach ($linhas as $key => $linha) {
        $dados = explode(',', $linha);
        if ($dados[0] === $usuario['email']) {
            $linhas[$key] = implode(',', [
                $usuario['email'],
                $usuario['nickname'],
                $usuario['senha_hash'],
                $usuario['saldo_moedas'],
                implode(';', $usuario['itens_comprados']),
                implode(';', $usuario['amigos']),
                implode(';', $usuario['solicitacoes_amizade'])
            ]);
            $encontrado = true;
            break;
        }
    }

    if (!$encontrado) {
        $linhas[] = implode(',', [
            $usuario['email'],
            $usuario['nickname'],
            $usuario['senha_hash'],
            $usuario['saldo_moedas'],
            implode(';', $usuario['itens_comprados']),
            implode(';', $usuario['amigos']),
            implode(';', $usuario['solicitacoes_amizade'])
        ]);
    }

    file_put_contents($clientes_file, implode(PHP_EOL, $linhas) . PHP_EOL);
}

$usuario_logado = carregarDadosUsuario($_SESSION['email']);
$saldo_moedas = $usuario_logado['saldo_moedas'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Meu Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos específicos para a página de perfil */
        .profile-section {
            padding: 60px 0;
            text-align: center;
            background-color: var(--background-dark);
        }

        .profile-card {
            background-color: var(--card-background);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            color: var(--text-color-light);
        }

        .profile-card h2 {
            font-family: var(--font-accent);
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .profile-card h2 i {
            color: var(--text-color-light);
        }

        .profile-info p {
            font-size: 1.2em;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .profile-info p strong {
            color: var(--accent-color);
        }

        .profile-actions {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .profile-actions .btn {
            width: 100%;
            padding: 12px 20px;
            font-size: 1.1em;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .profile-card {
                padding: 30px;
                margin: 0 20px;
            }
            .profile-card h2 {
                font-size: 2em;
            }
            .profile-info p {
                font-size: 1em;
                flex-direction: column;
                gap: 5px;
            }
        }

        @media (max-width: 480px) {
            .profile-card {
                padding: 20px;
                margin: 0 15px;
            }
            .profile-card h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">GameMaxStore</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="loja.php">Loja</a></li>
                    <li><a href="itens.php">Meus Itens</a></li>
                    <li><a href="amigos.php">Amigos</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <span class="user-balance">
                    <i class="fas fa-coins"></i> <span id="user-coins"><?= number_format($saldo_moedas, 0, ',', '.') ?></span> Moedas
                    <a href="comprar_moedas.php">+</a>
                </span>
                <a href="usuario.php" class="btn btn-secondary">
                    <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['nickname']) ?>
                </a>
            </div>
            <button class="menu-toggle" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main>
        <section class="profile-section">
            <div class="container">
                <div class="profile-card">
                    <h2><i class="fas fa-user-circle"></i> Meu Perfil</h2>
                    <div class="profile-info">
                        <p><i class="fas fa-user"></i> Nickname: <strong><?= htmlspecialchars($usuario_logado['nickname']) ?></strong></p>
                        <p><i class="fas fa-envelope"></i> E-mail: <strong><?= htmlspecialchars($usuario_logado['email']) ?></strong></p>
                        <p><i class="fas fa-coins"></i> Saldo de Moedas: <strong><?= number_format($usuario_logado['saldo_moedas'], 0, ',', '.') ?></strong></p>
                    </div>
                    <div class="profile-actions">
                        <a href="#" class="btn btn-primary"><i class="fas fa-edit"></i> Editar Dados da Conta</a>
                        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Sair</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
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
