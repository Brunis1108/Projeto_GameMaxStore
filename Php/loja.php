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

// Processar compra se o formulário foi submetido
$mensagem_compra = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comprar'])) {
    $pacote = $_POST['pacote'];
    $preco = floatval($_POST['preco']);
    $moedas = intval($_POST['moedas']);
    $quantidade = intval($_POST['quantidade']);
    
    $usuario_logado = carregarDadosUsuario($_SESSION['email']);
    
    $total_moedas = $moedas * $quantidade;
    $total_preco = $preco * $quantidade;
    
    if ($usuario_logado['saldo_moedas'] >= $total_moedas) {
        // Deduzir moedas
        $usuario_logado['saldo_moedas'] -= $total_moedas;
        
        // Adicionar item(s) ao inventário
        for ($i = 0; $i < $quantidade; $i++) {
            $usuario_logado['itens_comprados'][] = $pacote . ':' . $preco;
        }
        
        // Salvar dados
        salvarDadosUsuario($usuario_logado);
        date_default_timezone_set('America/Sao_Paulo');
        // Registrar a compra
        $log_file = 'Banco/compras.txt';
        $data_hora = date('Y-m-d H:i:s');
        $log_entry = "[$data_hora] Pacote: $pacote | Preço: R$ " . number_format($total_preco, 2, ',', '.') . 
                     " | Quantidade: $quantidade | Email: {$_SESSION['email']}\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND);
        
        $mensagem_compra = "Compra de {$quantidade}x '{$pacote}' realizada com sucesso!";
    } else {
        $mensagem_compra = "Você não tem moedas suficientes para esta compra!";
    }
}

$usuario_logado = carregarDadosUsuario($_SESSION['email']);
$saldo_moedas = $usuario_logado['saldo_moedas'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Loja</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/loja.css">
    <style>
        /* Estilo para a mensagem igual ao do index.php */
        .message-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            animation: slideIn 0.5s, fadeOut 0.5s 2.5s forwards;
        }
        
        .message-popup.success {
            background-color: #4CAF50;
        }
        
        .message-popup.error {
            background-color: #f44336;
        }
        
        @keyframes slideIn {
            from { right: -300px; opacity: 0; }
            to { right: 20px; opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body>
    <?php if ($mensagem_compra): ?>
        <div class="message-popup <?= strpos($mensagem_compra, 'sucesso') !== false ? 'success' : 'error' ?>">
            <?= $mensagem_compra ?>
        </div>
    <?php endif; ?>

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
                    <i class="fas fa-coins"></i> <?= number_format($saldo_moedas, 0, ',', '.') ?> Moedas
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
        <section class="promotions-section container">
            <h2><i class="fas fa-fire"></i> Produtos Disponíveis</h2>
            <div class="product-grid">
                <!-- Produto 1 -->
                <form method="POST" class="product-card">
                    <img src="../img/yunara.jpg" alt="Yunara">
                    <h3>Nova Campeã: Yunara, a fé inabalável</h3>
                    <p class="price">R$ 20,00</p>
                    <p class="coins-price">2.000 Moedas</p>
                    <input type="hidden" name="pacote" value="Nova Campeã: Yunara, a fé inabalável">
                    <input type="hidden" name="preco" value="20.00">
                    <input type="hidden" name="moedas" value="2000">
                    <div class="form-group">
                        <label for="quantidade1">Quantidade:</label>
                        <input type="number" id="quantidade1" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 2 -->
                <form method="POST" class="product-card">
                    <img src="../img/mel.jpg" alt="Skin Exclusiva">
                    <h3>Campeã Mel, o reflexo da alma</h3>
                    <p class="price">R$ 20,00</p>
                    <p class="coins-price">2.000 Moedas</p>
                    <input type="hidden" name="pacote" value="Campeã Mel, o reflexo da alma">
                    <input type="hidden" name="preco" value="20.00">
                    <input type="hidden" name="moedas" value="2000">
                    <div class="form-group">
                        <label for="quantidade2">Quantidade:</label>
                        <input type="number" id="quantidade2" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 3 -->
                <form method="POST" class="product-card">
                    <img src="../img/aurora.jpg" alt="Kit Inicial">
                    <h3>Campeã Aurora, a bruxa entre mundos</h3>
                    <p class="price">R$ 20,00</p>
                    <p class="coins-price">2.000 Moedas</p>
                    <input type="hidden" name="pacote" value="Campeã Aurora, a bruxa entre mundos">
                    <input type="hidden" name="preco" value="20.00">
                    <input type="hidden" name="moedas" value="2000">
                    <div class="form-group">
                        <label for="quantidade3">Quantidade:</label>
                        <input type="number" id="quantidade3" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 4 -->
                <form method="POST" class="product-card">
                    <img src="../img/fiddle.jpg" alt="Pacote de Moedas">
                    <h3>Skin Épica Fiddlesticks: Lua Sangrenta</h3>
                    <p class="price">R$ 50,00</p>
                    <p class="coins-price">5.000 Moedas</p>
                    <input type="hidden" name="pacote" value="Skin Épica Fiddlesticks: Lua Sangrenta">
                    <input type="hidden" name="preco" value="50.00">
                    <input type="hidden" name="moedas" value="5000">
                    <div class="form-group">
                        <label for="quantidade4">Quantidade:</label>
                        <input type="number" id="quantidade4" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 5 -->
                <form method="POST" class="product-card">
                    <img src="../img/naafiri.jpg" alt="Passe de Batalha">
                    <h3>Skin Lendária Naafiri: Soul Fighter</h3>
                    <p class="price">R$ 75,00</p>
                    <p class="coins-price">7.500 Moedas</p>
                    <input type="hidden" name="pacote" value="Skin Lendária Naafiri: Soul Fighter">
                    <input type="hidden" name="preco" value="75.00">
                    <input type="hidden" name="moedas" value="7500">
                    <div class="form-group">
                        <label for="quantidade5">Quantidade:</label>
                        <input type="number" id="quantidade5" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 6 -->
                <form method="POST" class="product-card">
                    <img src="../img/malph.png" alt="Emote Especial">
                    <h3>Skin Base Malphite: Whatsapp</h3>
                    <p class="price">R$ 15,00</p>
                    <p class="coins-price">1.500 Moedas</p>
                    <input type="hidden" name="pacote" value="Skin Base Malphite: Whatsapp">
                    <input type="hidden" name="preco" value="15.00">
                    <input type="hidden" name="moedas" value="1500">
                    <div class="form-group">
                        <label for="quantidade6">Quantidade:</label>
                        <input type="number" id="quantidade6" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 7 -->
                <form method="POST" class="product-card">
                    <img src="../IMG/rp.jpg" alt="Avatar Raro">
                    <h3>Pacote 800 Riot Points (RP)</h3>
                    <p class="price">R$ 12,00</p>
                    <p class="coins-price">1.200 Moedas</p>
                    <input type="hidden" name="pacote" value="Pacote 800 Riot Points (RP)">
                    <input type="hidden" name="preco" value="12.00">
                    <input type="hidden" name="moedas" value="1200">
                    <div class="form-group">
                        <label for="quantidade7">Quantidade:</label>
                        <input type="number" id="quantidade7" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 8 -->
                <form method="POST" class="product-card">
                    <img src="../img/vr.jpg" alt="Pacote de Boost">
                    <h3>Pacote 500 Valorant Points (VR)</h3>
                    <p class="price">R$ 15,00</p>
                    <p class="coins-price">1.500 Moedas</p>
                    <input type="hidden" name="pacote" value="Pacote 500 Valorant Points (VR)">
                    <input type="hidden" name="preco" value="15.00">
                    <input type="hidden" name="moedas" value="1500">
                    <div class="form-group">
                        <label for="quantidade8">Quantidade:</label>
                        <input type="number" id="quantidade8" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>

                <!-- Produto 9 -->
                <form method="POST" class="product-card">
                    <img src="../IMG/wc.jpeg" alt="Bundle Exclusivo">
                    <h3>Pacote 500 Wild Cores (WC)</h3>
                    <p class="price">R$ 15,00</p>
                    <p class="coins-price">1.500 Moedas</p>
                    <input type="hidden" name="pacote" value="Pacote 500 Wild Cores (WC)">
                    <input type="hidden" name="preco" value="15.00">
                    <input type="hidden" name="moedas" value="1500">
                    <div class="form-group">
                        <label for="quantidade9">Quantidade:</label>
                        <input type="number" id="quantidade9" name="quantidade" value="1" min="1">
                    </div>
                    <button type="submit" name="comprar" class="btn btn-primary">Comprar</button>
                </form>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>