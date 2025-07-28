<?php
session_start();

// Redirecionar para a página de login se o usuário não estiver logado
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

$saldo_moedas = 0;
if (isset($_SESSION['email'])) {
    $usuario_logado = carregarDadosUsuario($_SESSION['email']);
    if ($usuario_logado) {
        $saldo_moedas = $usuario_logado['saldo_moedas'];
    }
}
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
        <section class="promotions-section container">
            <h2><i class="fas fa-fire"></i> Produtos Disponíveis</h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="../img/pacote_lendario.png" alt="Pacote Lendário">
                    <div class="product-info">
                        <h3>Pacote Lendário</h3>
                        <p class="price">R$ 150,00</p>
                        <p class="coins-price">15.000 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Pacote Lendário" data-preco="150.00" data-moedas="15000">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/skin.jpg" alt="Skin Exclusiva">
                    <div class="product-info">
                        <h3>Skin Exclusiva</h3>
                        <p class="price">R$ 80,00</p>
                        <p class="coins-price">8.000 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Skin Exclusiva" data-preco="80.00" data-moedas="8000">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/kit_inicial.png" alt="Kit Inicial">
                    <div class="product-info">
                        <h3>Kit Inicial</h3>
                        <p class="price">R$ 50,00</p>
                        <p class="coins-price">5.000 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Kit Inicial" data-preco="50.00" data-moedas="5000">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/arma.jpeg" alt="Arma Épica">
                    <div class="product-info">
                        <h3>Arma Épica Sakura Vandal</h3>
                        <p class="price">R$ 120,00</p>
                        <p class="coins-price">12.000 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Arma Épica Sakura Vandal" data-preco="120.00" data-moedas="12000">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/emote.jpeg" alt="Emote Raro">
                    <div class="product-info">
                        <h3>Emote Raro "Ok" Rammus</h3>
                        <p class="price">R$ 30,00</p>
                        <p class="coins-price">3.000 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Emote Raro 'Ok' Rammus" data-preco="30.00" data-moedas="3000">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/pacote_skin.png" alt="Pacote de Skin">
                    <div class="product-info">
                        <h3>Pacote de Skin Luz e Escuridão - Hecarim, Kalista, Yuumi</h3>
                        <p class="price">R$ 75,00</p>
                        <p class="coins-price">7.500 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Pacote de Skin Luz e Escuridão - Hecarim, Kalista, Yuumi" data-preco="75.00" data-moedas="7500">Comprar</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-links">
                <ul>
                    <li><a href="#">Sobre Nós</a></li>
                    <li><a href="#">Termos de Serviço</a></li>
                    <li><a href="#">Política de Privacidade</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Modal de Confirmação -->
    <div id="modal-confirmacao" class="modal">
        <div class="modal-content">
            <h2>Confirmar Compra</h2>
            <p id="mensagem-modal"></p>
            <div class="form-group">
                <label for="quantidade-unidades">Quantidade:</label>
                <input type="number" id="quantidade-unidades" value="1" min="1">
            </div>
            <p id="valor-total-compra"></p>
            <div class="modal-actions">
                <button id="btn-confirmar" class="btn btn-primary">Confirmar</button>
                <button id="btn-cancelar" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        // JavaScript para o menu responsivo (exemplo simples)
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });

        let dadosCompra = {};
        const modal = document.getElementById('modal-confirmacao');
        const mensagem = document.getElementById('mensagem-modal');
        const quantidadeUnidadesInput = document.getElementById('quantidade-unidades');
        const valorTotalCompraSpan = document.getElementById('valor-total-compra');
        const btnConfirmar = document.getElementById('btn-confirmar');
        const btnCancelar = document.getElementById('btn-cancelar');
        const userCoinsSpan = document.getElementById('user-coins');

        // Função para atualizar o valor total da compra no modal
        function atualizarValorTotal() {
            const quantidade = parseInt(quantidadeUnidadesInput.value);
            const precoUnitarioMoedas = dadosCompra.moedas;
            const precoUnitarioReal = dadosCompra.preco;

            const totalMoedas = quantidade * precoUnitarioMoedas;
            const totalReal = quantidade * precoUnitarioReal;

            valorTotalCompraSpan.textContent = `Total: ${totalMoedas.toLocaleString('pt-BR')} Moedas (R$ ${totalReal.toFixed(2).replace('.', ',')})`;
        }

        // Ao clicar em qualquer botão de comprar
        document.querySelectorAll('.btn-buy').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Pega dados do item
                dadosCompra = {
                    pacote: this.dataset.pacote,
                    preco: parseFloat(this.dataset.preco), // Converte para número
                    moedas: parseInt(this.dataset.moedas), // Converte para número
                    email: '<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>'
                };

                // Reseta a quantidade para 1 e atualiza o valor total
                quantidadeUnidadesInput.value = 1;
                atualizarValorTotal();

                // Atualiza mensagem e abre modal
                mensagem.textContent = `Deseja realmente comprar o pacote "${dadosCompra.pacote}"?`;
                modal.classList.add('active'); // Adiciona a classe 'active' para mostrar o modal
            });
        });

        // Event listener para mudanças na quantidade de unidades
        quantidadeUnidadesInput.addEventListener('input', atualizarValorTotal);

        // Cancelar compra
        btnCancelar.addEventListener('click', () => {
            modal.classList.remove('active'); // Remove a classe 'active' para esconder o modal
            dadosCompra = {};
        });

        // Confirmar compra
        btnConfirmar.addEventListener('click', () => {
            const quantidade = parseInt(quantidadeUnidadesInput.value);
            const saldoAtual = parseInt(userCoinsSpan.textContent.replace(/\./g, '')); // Remove pontos para converter
            const totalMoedasNecessarias = quantidade * dadosCompra.moedas;

            if (saldoAtual < totalMoedasNecessarias) {
                alert('Você não tem moedas suficientes para esta compra!');
                modal.classList.remove('active');
                return;
            }

            modal.classList.remove('active'); // Remove a classe 'active' para esconder o modal

            fetch('registrar_compra.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        pacote: dadosCompra.pacote,
                        preco: dadosCompra.preco,
                        moedas: dadosCompra.moedas,
                        email: dadosCompra.email,
                        quantidade: quantidade // Envia a quantidade para o backend
                    })
                })
                .then(response => response.json()) // Espera JSON como resposta
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        userCoinsSpan.textContent = data.novo_saldo.toLocaleString('pt-BR'); // Atualiza o saldo na UI
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao registrar compra.');
                });
        });
    </script>
</body>
</html>
