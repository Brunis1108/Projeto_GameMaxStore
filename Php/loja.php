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
                    <li><a href="#">Loja</a></li>
                    <li><a href="#">Meus Itens</a></li>
                    <li><a href="#">Amigos</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="registro.php" class="btn btn-secondary">Cadastre-se</a>
                <span class="user-balance">
                    <i class="fas fa-coins"></i> 10.000 Moedas
                </span>
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
                    <img src="https://via.placeholder.com/300x200?text=Pacote+Lendario" alt="Pacote Lendário">
                    <h3>Pacote Lendário</h3>
                    <p class="price">R$ 150,00</p>
                    <p class="coins-price">15.000 Moedas</p>
                    <button class="btn btn-buy" onclick="showLoginPrompt()">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x200?text=Skin+Exclusiva" alt="Skin Exclusiva">
                    <h3>Skin Exclusiva</h3>
                    <p class="price">R$ 80,00</p>
                    <p class="coins-price">8.000 Moedas</p>
                    <button class="btn btn-buy" onclick="showLoginPrompt()">Comprar</button>
                </div>
                <div class="product-card">
                    <img src="https://via.placeholder.com/300x200?text=Kit+Inicial" alt="Kit Inicial">
                    <h3>Kit Inicial</h3>
                    <p class="price">R$ 50,00</p>
                    <p class="coins-price">5.000 Moedas</p>
                    <button class="btn btn-buy" onclick="showLoginPrompt()">Comprar</button>
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
                    </div>
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
        </div>
                </ul>
            </div>
            <div class="