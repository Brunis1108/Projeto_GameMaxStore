<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Função para carregar dados do usuário com valores padrão
function carregarDadosUsuario($email) {
    $clientes_file = 'clientes.txt';
    if (file_exists($clientes_file)) {
        $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($clientes as $cliente) {
            $dados = explode(',', $cliente);
            if ($dados[0] === $email) {
                return [
                    'email' => $dados[0] ?? '',
                    'nickname' => $dados[1] ?? '',
                    'senha_hash' => $dados[2] ?? '',
                    'saldo_moedas' => isset($dados[3]) ? (int)$dados[3] : 0,
                    'itens_comprados' => isset($dados[4]) && $dados[4] !== '' ? explode(';', $dados[4]) : [],
                    'amigos' => isset($dados[5]) && $dados[5] !== '' ? explode(';', $dados[5]) : [],
                    'solicitacoes_amizade' => isset($dados[6]) && $dados[6] !== '' ? explode(';', $dados[6]) : []
                ];
            }
        }
    }
    // Retorna array com valores padrão se usuário não for encontrado
    return [
        'email' => $email,
        'nickname' => '',
        'senha_hash' => '',
        'saldo_moedas' => 0,
        'itens_comprados' => [],
        'amigos' => [],
        'solicitacoes_amizade' => []
    ];
}

// Função para salvar dados do usuário com verificação de arrays
function salvarDadosUsuario($usuario) {
    // Garante que os arrays existam
    $usuario['itens_comprados'] = $usuario['itens_comprados'] ?? [];
    $usuario['amigos'] = $usuario['amigos'] ?? [];
    $usuario['solicitacoes_amizade'] = $usuario['solicitacoes_amizade'] ?? [];
    
    $clientes_file = 'clientes.txt';
    $linhas = [];
    $encontrado = false;

    if (file_exists($clientes_file)) {
        $linhas = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }

    foreach ($linhas as $key => $linha) {
        $dados = explode(',', $linha);
        if (isset($dados[0]) && $dados[0] === $usuario['email']) {
            $linhas[$key] = implode(',', [
                $usuario['email'],
                $usuario['nickname'],
                $usuario['senha_hash'],
                $usuario['saldo_moedas'],
                !empty($usuario['itens_comprados']) ? implode(';', $usuario['itens_comprados']) : '',
                !empty($usuario['amigos']) ? implode(';', $usuario['amigos']) : '',
                !empty($usuario['solicitacoes_amizade']) ? implode(';', $usuario['solicitacoes_amizade']) : ''
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
            !empty($usuario['itens_comprados']) ? implode(';', $usuario['itens_comprados']) : '',
            !empty($usuario['amigos']) ? implode(';', $usuario['amigos']) : '',
            !empty($usuario['solicitacoes_amizade']) ? implode(';', $usuario['solicitacoes_amizade']) : ''
        ]);
    }

    file_put_contents($clientes_file, implode(PHP_EOL, $linhas) . PHP_EOL);
}

// Carrega dados do usuário com valores padrão
$usuario_logado = carregarDadosUsuario($_SESSION['email']);
$saldo_moedas = $usuario_logado['saldo_moedas'] ?? 0;
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantidade_moedas = intval($_POST['quantidade_moedas'] ?? 0);
    $valor_real = floatval($_POST['valor_real'] ?? 0);

    if ($quantidade_moedas > 0 && $valor_real > 0) {
        $usuario_logado['saldo_moedas'] += $quantidade_moedas;
        salvarDadosUsuario($usuario_logado);
        
        $mensagem = "Parabéns! Você comprou " . number_format($quantidade_moedas, 0, ',', '.') . 
                   " moedas por R$ " . number_format($valor_real, 2, ',', '.') . 
                   ". Seu novo saldo é: " . number_format($usuario_logado['saldo_moedas'], 0, ',', '.') . " moedas.";
        $saldo_moedas = $usuario_logado['saldo_moedas'];
    } else {
        $mensagem = "Por favor, insira uma quantidade válida de moedas.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Comprar Moedas</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/comprar_moedas.css">
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

    <main class="buy-coins-page">
        <div class="container">
            <div class="buy-coins-card">
                <h2><i class="fas fa-coins"></i> Comprar Moedas</h2>
                <p class="current-balance">Seu saldo atual: <span id="current-coins"><?= number_format($saldo_moedas, 0, ',', '.') ?></span> Moedas</p>

                <?php if ($mensagem): ?>
                    <div class="message <?= strpos($mensagem, 'Parabéns') !== false ? 'success' : 'error' ?>">
                        <?= $mensagem ?>
                    </div>
                <?php endif; ?>

                <form action="comprar_moedas.php" method="POST" class="buy-coins-form">
                    <div class="form-group">
                        <label for="quantidade_moedas">Quantidade de Moedas:</label>
                        <input type="number" id="quantidade_moedas" name="quantidade_moedas" min="100" step="100" value="100" required>
                    </div>
                    <div class="form-group">
                        <label for="valor_real">Valor em Reais (R$):</label>
                        <input type="text" id="valor_real" name="valor_real" readonly>
                    </div>
                    <button type="submit" class="btn btn-primary">Comprar Agora</button>
                </form>

                <div class="coin-packages">
                    <h3>Pacotes Sugeridos:</h3>
                    <div class="package-grid">
                        <div class="package-card" data-moedas="500" data-valor="5.00">
                            <i class="fas fa-gem"></i>
                            <h4>500 Moedas</h4>
                            <p>R$ 5,00</p>
                        </div>
                        <div class="package-card" data-moedas="1000" data-valor="9.50">
                            <i class="fas fa-gem"></i>
                            <h4>1.000 Moedas</h4>
                            <p>R$ 9,50 <span class="discount">(5% OFF)</span></p>
                        </div>
                        <div class="package-card" data-moedas="5000" data-valor="45.00">
                            <i class="fas fa-gem"></i>
                            <h4>5.000 Moedas</h4>
                            <p>R$ 45,00 <span class="discount">(10% OFF)</span></p>
                        </div>
                        <div class="package-card" data-moedas="10000" data-valor="85.00">
                            <i class="fas fa-gem"></i>
                            <h4>10.000 Moedas</h4>
                            <p>R$ 85,00 <span class="discount">(15% OFF)</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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

        const quantidadeMoedasInput = document.getElementById('quantidade_moedas');
        const valorRealInput = document.getElementById('valor_real');
        const packageCards = document.querySelectorAll('.package-card');
        const currentCoinsSpan = document.getElementById('current-coins');

        // Função para calcular o valor em reais
        function calcularValorReal() {
            const quantidade = parseInt(quantidadeMoedasInput.value);
            let valor = 0;

            // Exemplo de tabela de preços (pode ser mais complexa)
            if (quantidade >= 10000) {
                valor = quantidade * 0.0085; // 15% de desconto
            } else if (quantidade >= 5000) {
                valor = quantidade * 0.009; // 10% de desconto
            } else if (quantidade >= 1000) {
                valor = quantidade * 0.0095; // 5% de desconto
            } else {
                valor = quantidade * 0.01; // Preço base: 1 moeda = R$ 0.01
            }
            valorRealInput.value = valor.toFixed(2).replace('.', ',');
        }

        // Atualiza o valor real ao mudar a quantidade de moedas
        quantidadeMoedasInput.addEventListener('input', calcularValorReal);

        // Preenche os campos ao clicar em um pacote sugerido
        packageCards.forEach(card => {
            card.addEventListener('click', () => {
                quantidadeMoedasInput.value = card.dataset.moedas;
                valorRealInput.value = parseFloat(card.dataset.valor).toFixed(2).replace('.', ',');
            });
        });

        // Inicializa o valor real ao carregar a página
        calcularValorReal();
    </script>
</body>
</html>