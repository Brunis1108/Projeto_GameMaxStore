<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Cadastre-se</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- Reutiliza o CSS principal -->
    <link rel="stylesheet" href="../css/registro.css"> <!-- CSS específico para o formulário -->
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
                    <li><a href="#">Loja</a></li>
                    <li><a href="#">Meus Itens</a></li>
                    <li><a href="#">Amigos</a></li>
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="login.php" class="btn btn-primary">Login</a>
                <span class="user-balance">
                    <i class="fas fa-coins"></i> 10.000 Moedas
                </span>
            </div>
            <button class="menu-toggle" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main class="auth-page">
        <div class="auth-container">
            <h2 class="cadastro"><i ></i> Cadastre-se na GameMaxStore</h2>
            <form class="auth-form" action="#" method="POST">
                <div class="form-group">
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required>
                </div>
                <div class="form-group">
                    <label for="nickname">Nickname:</label>
                    <input type="text" id="nickname" name="nickname" placeholder="SeuNickGame" required>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>
                <div class="form-group">
                    <label for="confirm-password">Confirmar Senha:</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-call-to-action">Criar Conta</button>
            </form>
            <p class="auth-link">Já tem uma conta? <a href="login.php">Faça Login aqui!</a></p>
        </div>
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
        // JavaScript para o menu responsivo (reutilizado)
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    </script>
</body>
</html>
