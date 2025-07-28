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
$amigos_usuario = $usuario_logado['amigos'];
$solicitacoes_recebidas = $usuario_logado['solicitacoes_amizade'];

$mensagem_busca = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar_amigo'])) {
    $nickname_busca = trim($_POST['nickname_busca']);
    if ($nickname_busca === $usuario_logado['nickname']) {
        $mensagem_busca = "Você não pode adicionar a si mesmo!";
    } elseif (in_array($nickname_busca, $amigos_usuario)) {
        $mensagem_busca = "Você já é amigo de " . htmlspecialchars($nickname_busca) . "!";
    } elseif (in_array($nickname_busca, $solicitacoes_recebidas)) {
        $mensagem_busca = htmlspecialchars($nickname_busca) . " já te enviou uma solicitação de amizade. Verifique suas solicitações!";
    } else {
        $encontrou_nick = false;
        $clientes_file = 'clientes.txt';
        if (file_exists($clientes_file)) {
            $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($clientes as $cliente) {
                $dados_cliente = explode(',', $cliente);
                if ($dados_cliente[1] === $nickname_busca) { // Nickname na posição 1
                    $encontrou_nick = true;
                    // Enviar solicitação
                    $amigo_encontrado_email = $dados_cliente[0];
                    $amigo_encontrado_dados = carregarDadosUsuario($amigo_encontrado_email);

                    if (!in_array($usuario_logado['nickname'], $amigo_encontrado_dados['solicitacoes_amizade'])) {
                        $amigo_encontrado_dados['solicitacoes_amizade'][] = $usuario_logado['nickname'];
                        salvarDadosUsuario($amigo_encontrado_dados);
                        $mensagem_busca = "Solicitação de amizade enviada para " . htmlspecialchars($nickname_busca) . "!";
                    } else {
                        $mensagem_busca = "Você já enviou uma solicitação de amizade para " . htmlspecialchars($nickname_busca) . "!";
                    }
                    break;
                }
            }
        }
        if (!$encontrou_nick) {
            $mensagem_busca = "Nickname não encontrado!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Amigos</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/amigos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        <section class="friends-section container">
            <h2><i class="fas fa-users"></i> Meus Amigos</h2>

            <div class="friends-menu">
                <div class="menu-section">
                    <h3><i class="fas fa-user-friends"></i> Meus Amigos</h3>
                    <?php if (empty($amigos_usuario)): ?>
                        <p class="no-friends-message">Você ainda não tem amigos. Que tal adicionar alguns?</p>
                    <?php else: ?>
                        <ul class="friend-list">
                            <?php foreach ($amigos_usuario as $amigo): ?>
                                <li><?= htmlspecialchars($amigo) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="menu-section">
                    <h3><i class="fas fa-search"></i> Procurar Amigo</h3>
                    <form action="amigos.php" method="POST" class="search-friend-form">
                        <div class="form-group">
                            <input type="text" id="nickname_busca" name="nickname_busca" placeholder="Nickname do amigo" required>
                        </div>
                        <button type="submit" name="buscar_amigo" class="btn btn-primary">Buscar e Enviar Solicitação</button>
                    </form>
                    <?php if ($mensagem_busca): ?>
                        <p class="search-message <?= strpos($mensagem_busca, 'não encontrado') !== false || strpos($mensagem_busca, 'não pode adicionar') !== false ? 'error' : 'success' ?>">
                            <?= $mensagem_busca ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="menu-section">
                    <h3><i class="fas fa-bell"></i> Solicitações de Amizade Recebidas</h3>
                    <?php if (empty($solicitacoes_recebidas)): ?>
                        <p class="no-requests-message">Nenhuma solicitação de amizade pendente.</p>
                    <?php else: ?>
                        <ul class="friend-request-list">
                            <?php foreach ($solicitacoes_recebidas as $solicitante): ?>
                                <li>
                                    <span><?= htmlspecialchars($solicitante) ?></span>
                                    <div class="request-actions">
                                        <button class="btn btn-accept" data-solicitante="<?= htmlspecialchars($solicitante) ?>">Aceitar</button>
                                        <button class="btn btn-decline" data-solicitante="<?= htmlspecialchars($solicitante) ?>">Recusar</button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
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

        // Lógica para aceitar/recusar solicitações de amizade
        document.querySelectorAll('.btn-accept').forEach(btn => {
            btn.addEventListener('click', function() {
                const solicitante = this.dataset.solicitante;
                fetch('processar_solicitacao_amizade.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'accept',
                        solicitante: solicitante
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // Recarrega a página para atualizar as listas
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar solicitação.');
                });
            });
        });

        document.querySelectorAll('.btn-decline').forEach(btn => {
            btn.addEventListener('click', function() {
                const solicitante = this.dataset.solicitante;
                fetch('processar_solicitacao_amizade.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'decline',
                        solicitante: solicitante
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // Recarrega a página para atualizar as listas
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar solicitação.');
                });
            });
        });
    </script>
</body>
</html>
