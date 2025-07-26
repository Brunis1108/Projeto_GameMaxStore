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
                <a href="index.html">GameMaxStore</a>
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
                <a href="#" class="btn btn-primary">Login</a>
                <a href="regsitro.html" class="btn btn-secondary">Cadastre-se</a>
                <span class="user-balance">
                    <i class="fas fa-coins"></i> 10.000 Moedas
                    <a href="#">+</a>
                </span>
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
                <a href="l" class="btn btn-call-to-action">Ver Ofertas Agora!</a>
            </div>
        </section>

        <section class="promotions-section container">
            <h2><i class="fas fa-fire"></i> Promoções Imperdíveis</h2>
            <div class="product-grid">
                <div class="product-card promotion">
                    <span class="promo-badge">-30%</span>
                    <img src="../img/pacote_lendario.png" alt="Pacote Lendário">
                    <h3>Pacote Lendário</h3>
                    <p class="price">De: <del>R$ 150,00</del> Por: R$ 105,00</p>
                    <p class="coins-price">10.500 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Pacote Lendário" data-preco="105.00">Comprar</a>
                </div>
                <div class="product-card promotion">
                    <span class="promo-badge">-20%</span>
                    <img src="../img/skin.jpg" alt="Skin Exclusiva">
                    <h3>Skin Exclusiva</h3>
                    <p class="price">De: <del>R$ 80,00</del> Por: R$ 64,00</p>
                    <p class="coins-price">6.400 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Skin Exclusiva" data-preco="64.00">Comprar</a>
                </div>
                <div class="product-card promotion">
                    <span class="promo-badge">-15%</span>
                    <img src="../img/kit_inicial.png" alt="Kit Inicial">
                    <h3>Kit Inicial</h3>
                    <p class="price">De: <del>R$ 50,00</del> Por: R$ 42,50</p>
                    <p class="coins-price">4.250 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Kit Inicial" data-preco="42.50">Comprar</a>
                </div>
            </div>
        </section>

        <section class="new-items-section container">
            <h2><i class="fas fa-star"></i> Novidades na Loja</h2>
            <div class="product-grid">
                <div class="product-card">
                    <img src="../img/arma.jpeg" alt="Arma Épica">
                    <h3>Arma Épica</h3>
                    <p class="price">R$ 120,00</p>
                    <p class="coins-price">12.000 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Arma Épica" data-preco="120.00">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/emote.jpeg" alt="Emote Raro">
                    <h3>Emote Raro</h3>
                    <p class="price">R$ 30,00</p>
                    <p class="coins-price">3.000 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Emote Raro" data-preco="30.00">Comprar</a>
                </div>
                <div class="product-card">
                    <img src="../img/pacote_skin.png" alt="Pacote de Skin">
                    <h3>Pacote de Skin</h3>
                    <p class="price">R$ 75,00</p>
                    <p class="coins-price">7.500 Moedas</p>
                    <a href="#" class="btn btn-buy" data-pacote="Pacote de Skin" data-preco="75.00">Comprar</a>
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

    <script>
        // JavaScript para o menu responsivo (exemplo simples)
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    </script>
    <script>
        document.querySelectorAll('.btn-buy').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const pacote = this.dataset.pacote;
                const preco = this.dataset.preco;
                let email = this.dataset.email || '';

                const confirmacao = confirm(`Certeza que deseja comprar o pacote "${pacote}" por R$ ${preco}?`);

                if (confirmacao) {
                    fetch('registrar_compra.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                pacote: pacote,
                                preco: preco,
                                email: email
                            })
                        })
                        .then(response => response.text())
                        .then(msg => alert(msg))
                        .catch(err => alert('Erro ao registrar compra.'));
                }
            });
        });
    </script>

</body>

</html>