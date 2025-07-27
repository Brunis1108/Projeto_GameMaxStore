<?php
session_start();

// Redirecionar para a página de login se o usuário não estiver logado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
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
                <a href="usuario.php">GameMaxStore</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">Loja</a></li>
                    <li><a href="#">Meus Itens</a></li>
                    <li><a href="#">Amigos</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <span class="user-balance">
                    <i class="fas fa-coins"></i> 10.000 Moedas
                </span>
                <a href="logout.php" class="btn btn-secondary">Sair</a>
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
                    <h3>Pacote Lendário</h3>
                    <p class="price">R$ 150,00</p>
                    <p class="coins-price">15.000 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Pacote Lendário" data-preco="150.00">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="../img/skin.jpg" alt="Skin Exclusiva">
                    <h3>Skin Exclusiva</h3>
                    <p class="price">R$ 80,00</p>
                    <p class="coins-price">8.000 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Skin Exclusiva" data-preco="80.00">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="../img/kit_inicial.png" alt="Kit Inicial">
                    <h3>Kit Inicial</h3>
                    <p class="price">R$ 50,00</p>
                    <p class="coins-price">5.000 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Kit Inicial" data-preco="50.00">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="../img/arma.jpeg" alt="Arma Épica">
                    <h3>Arma Épica Sakura Vandal</h3>
                    <p class="price">R$ 120,00</p>
                    <p class="coins-price">12.000 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Arma Épica" data-preco="120.00">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="../img/emote.jpeg" alt="Emote Raro">
                    <h3>Emote Raro "Ok" Rammus</h3>
                    <p class="price">R$ 30,00</p>
                    <p class="coins-price">3.000 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Emote Raro" data-preco="30.00">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="../img/pacote_skin.png" alt="Pacote de Skin">
                    <h3>Pacote de Skin Luz e Escuridão - Hecarim, Kalista, Yuumi</h3>
                    <p class="price">R$ 75,00</p>
                    <p class="coins-price">7.500 Moedas</p>
                    <button class="btn btn-buy" data-pacote="Pacote de Skin" data-preco="75.00">Comprar</button>
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
        const btnConfirmar = document.getElementById('btn-confirmar');
        const btnCancelar = document.getElementById('btn-cancelar');

        // Ao clicar em qualquer botão de comprar
        document.querySelectorAll('.btn-buy').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                // Verifica se o usuário está logado (já garantido pelo PHP no topo do arquivo)
                // Pega dados do item
                dadosCompra = {
                    pacote: this.dataset.pacote,
                    preco: this.dataset.preco,
                    email: '<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>'
                };

                // Atualiza mensagem e abre modal
                mensagem.textContent = `Deseja realmente comprar o pacote "${dadosCompra.pacote}" por R$ ${dadosCompra.preco}?`;
                modal.classList.add('active'); // Adiciona a classe 'active' para mostrar o modal
            });
        });

        // Cancelar compra
        btnCancelar.addEventListener('click', () => {
            modal.classList.remove('active'); // Remove a classe 'active' para esconder o modal
            dadosCompra = {};
        });

        // Confirmar compra
        btnConfirmar.addEventListener('click', () => {
            modal.classList.remove('active'); // Remove a classe 'active' para esconder o modal

            fetch('registrar_compra.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(dadosCompra)
                })
                .then(response => response.text())
                .then(msg => alert(msg))
                .catch(() => alert('Erro ao registrar compra.'));
        });
    </script>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pacote = $_POST['pacote'];
    $preco = $_POST['preco'];
    $email = $_POST['email'];

    // Aqui você pode adicionar a lógica para registrar a compra
    // Por exemplo, salvar em um banco de dados ou arquivo
    $compras_file = 'compras.txt';
    $compra_data = "$email,$pacote,$preco\n";
    file_put_contents($compras_file, $compra_data, FILE_APPEND);

    echo "Compra registrada com sucesso!";
    exit;
}
?>