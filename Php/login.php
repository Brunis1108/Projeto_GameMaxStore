<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">GameMaxStore</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <!-- Links de navegação para usuários não logados -->
                    <li><a href="#">Suporte</a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="registro.php" class="btn btn-secondary">Cadastre-se</a>
            </div>
            <button class="menu-toggle" aria-label="Abrir Menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </header>

    <main>
        <section class="login-section" id="login">
            <div class="container">
                <h1>Login</h1>
                <form action="#" method="POST" class="login-form">
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Senha:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <p class="register-link">Não tem uma conta? <a href="registro.php">Cadastre-se</a></p>
                </form>
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
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        // JavaScript para o menu responsivo (exemplo simples)
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });
    </script>
</body>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ler dados do arquivo de clientes
    $clientes_file = 'clientes.txt';
    $login_successful = false;
    $is_admin = false; // Flag para verificar se é admin

    if (file_exists($clientes_file)) {
        $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($clientes as $cliente) {
            list($stored_email, $nickname, $stored_password) = explode(',', $cliente);
            if ($stored_email === $email && password_verify($password, $stored_password)) {
                $_SESSION['email'] = $stored_email;
                $_SESSION['nickname'] = $nickname; // Armazenar o nickname na sessão

                // Verificar se é o administrador (exemplo: email específico para admin)
                if ($stored_email === 'admin@gamemax.com') { // Substitua pelo email do seu admin
                    $is_admin = true;
                }
                $login_successful = true;
                break;
            }
        }
    }

    if ($login_successful) {
        if ($is_admin) {
            header('Location: admin.php'); // Redirecionar para a página do admin
        } else {
            header('Location: index.php'); // Redirecionar para a página inicial (index.php)
        }
        exit;
    } else {
        // Se não encontrar, exibir mensagem de erro
        echo "<script>alert('E-mail ou senha inválidos!');</script>";
    }
}
?>
<script>
    // JavaScript para o menu responsivo (reutilizado)
    document.querySelector('.menu-toggle').addEventListener('click', function() {
        document.querySelector('.main-nav').classList.toggle('active');
    });
</script>
