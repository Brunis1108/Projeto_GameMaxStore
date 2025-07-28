<?php
session_start();

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

$saldo_moedas = 0; // Valor padrão para não logado
$notificacoes_presente = [];

if (isset($_SESSION['email'])) {
    $usuario_logado = carregarDadosUsuario($_SESSION['email']);
    if ($usuario_logado) {
        $saldo_moedas = $usuario_logado['saldo_moedas'];
        // Carregar notificações de presente
        $notificacoes_file = 'notificacoes.txt';
        if (file_exists($notificacoes_file)) {
            $linhas_notificacoes = file($notificacoes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($linhas_notificacoes as $linha_notificacao) {
                $partes = explode('|', $linha_notificacao);
                if (count($partes) === 3 && trim($partes[0]) === $_SESSION['nickname']) {
                    $notificacoes_presente[] = ['remetente' => trim($partes[1]), 'item' => trim($partes[2])];
                }
            }
            // Limpar notificações após carregar
            $novas_linhas_notificacoes = [];
            foreach ($linhas_notificacoes as $linha_notificacao) {
                $partes = explode('|', $linha_notificacao);
                if (count($partes) === 3 && trim($partes[0]) !== $_SESSION['nickname']) {
                    $novas_linhas_notificacoes[] = $linha_notificacao;
                }
            }
            file_put_contents($notificacoes_file, implode(PHP_EOL, $novas_linhas_notificacoes) . PHP_EOL);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Sua Loja de Itens do Jogo</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <!-- Ícones (ex: Font Awesome) -->
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
                    <?php if (isset($_SESSION['email'])): ?>
                        <li><a href="loja.php">Loja</a></li>
                        <li><a href="itens.php">Meus Itens</a></li>
                        <li><a href="amigos.php">Amigos</a></li>
                    <?php endif; ?>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <?php if (isset($_SESSION['email'])): ?>
                    <span class="user-balance">
                        <i class="fas fa-coins"></i> <span id="user-coins"><?= number_format($saldo_moedas, 0, ',', '.') ?></span> Moedas
                        <a href="comprar_moedas.php">+</a>
                    </span>
                    <a href="usuario.php" class="btn btn-secondary">
                        <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['nickname']) ?>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="registro.php" class="btn btn-secondary">Cadastre-se</a>
                <?php endif; ?>
            </div>
            <button class="menu-toggle" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main>
        <section class="hero-section">
            <div class="container">
                <h1>Novos Pacotes Chegaram!</h1>
                <p>Aproveite as ofertas exclusivas e turbine sua experiência no jogo.</p>
                <!-- Botão de loja removido da página inicial, acessível apenas após login -->
            </div>
        </section>

        <section class="promotions-section container">
            <h2><i class="fas fa-fire"></i> Promoções Imperdíveis</h2>
            <div class="product-grid">
                <div class="product-card promotion">
                    <span class="promo-badge">-30%</span>
                    <img src="../img/pacote_lendario.png" alt="Pacote Lendário">

                    <div class="product-info">
                        <h3>Pacote Lendário Hall Of Legends 2025 Uzi</h3>
                        <p class="price">De: <del>R$ 150,00</del> Por: R$ 105,00</p>
                        <p class="coins-price">10.500 Moedas</p>
                    </div>

                    <a href="#" class="btn btn-buy" data-pacote="Pacote Lendário Hall Of Legends 2025 Uzi" data-preco="105.00" data-moedas="10500">Comprar</a>
                </div>

                <div class="product-card promotion">
                    <span class="promo-badge">-20%</span>
                    <img src="../img/skin.jpg" alt="Skin Exclusiva">

                    <div class="product-info">
                        <h3>Skin Exclusiva Volibear: Urso dos Mil Flagelos</h3>
                        <p class="price">De: <del>R$ 80,00</del> Por: R$ 64,00</p>
                        <p class="coins-price">6.400 Moedas</p>
                    </div>
                    <a href="#" class="btn btn-buy" data-pacote="Skin Exclusiva Volibear: Urso dos Mil Flagelos" data-preco="64.00" data-moedas="6400">Comprar</a>
                </div>

                <div class="product-card promotion">
                    <span class="promo-badge">-15%</span>
                    <img src="../img/kit_inicial.png" alt="Kit Inicial">

                    <div class="product-info">
                        <h3>Kit Jogador Inicial</h3>
                        <p class="price">De: <del>R$ 50,00</del> Por: R$ 42,50</p>
                        <p class="coins-price">4.250 Moedas</p>
                    </div>

                    <a href="#" class="btn btn-buy" data-pacote="Kit Jogador Inicial" data-preco="42.50" data-moedas="4250">Comprar</a>
                </div>
            </div>
        </section>

        <section class="new-items-section container">
            <h2><i class="fas fa-star"></i> Novidades na Loja</h2>
            <div class="product-grid">
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
                    <h3>Pacote de Skin Luz e Escuridão - Hecarim, Kalista, Yuumi</h3>
                    <p class="price">R$ 75,00</p>
                    <p class="coins-price">7.500 Moedas</p>
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
            <div class="social-media">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
            <p>&copy; 2023 GameMaxStore. Todos os direitos reservados.</p>
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

    <!-- Modal de Notificações -->
    <div id="modal-notificacoes" class="modal">
        <div class="modal-content">
            <h2>Novos Presentes!</h2>
            <ul id="lista-notificacoes"></ul>
            <div class="modal-actions">
                <button id="btn-fechar-notificacoes" class="btn btn-primary">Fechar</button>
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

                // Verifica se o usuário está logado
                <?php if (!isset($_SESSION['email'])): ?>
                    alert('Você precisa estar logado para realizar uma compra!');
                    window.location.href = 'login.php'; // Redireciona para a página de login
                    return;
                <?php endif; ?>

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

        // Lógica para exibir notificações de presente
        const notificacoes = <?php echo json_encode($notificacoes_presente); ?>;
        const modalNotificacoes = document.getElementById('modal-notificacoes');
        const listaNotificacoes = document.getElementById('lista-notificacoes');
        const btnFecharNotificacoes = document.getElementById('btn-fechar-notificacoes');

        if (notificacoes.length > 0) {
            notificacoes.forEach(notificacao => {
                const li = document.createElement('li');
                li.textContent = `Você recebeu um presente de ${notificacao.remetente}: ${notificacao.item}`;
                listaNotificacoes.appendChild(li);
            });
            modalNotificacoes.classList.add('active');
        }

        btnFecharNotificacoes.addEventListener('click', () => {
            modalNotificacoes.classList.remove('active');
        });
    </script>

</body>

</html>
