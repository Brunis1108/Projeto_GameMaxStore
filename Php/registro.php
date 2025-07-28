    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>GameMaxStore - Cadastre-se</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/registro.css">
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
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $nickname = $_POST['nickname'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validação de senhas
        if ($password !== $confirm_password) {
            echo "<script>alert('As senhas não coincidem!');</script>";
            exit;
        }

        $clientes_file = 'clientes.txt';
        $is_nickname_taken = false;
        $is_email_taken = false;

        // Verificar se o arquivo de clientes existe
        if (file_exists($clientes_file)) {
            $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($clientes as $cliente) {
                list($stored_email, $stored_nickname, $stored_password_hash) = explode(',', $cliente);
                if ($stored_nickname === $nickname) {
                    $is_nickname_taken = true;
                    break;
                }
                if ($stored_email === $email) {
                    $is_email_taken = true;
                    break;
                }
            }
        }

        if ($is_nickname_taken) {
            echo "<script>alert('Nickname já está sendo usado, escolha outro nickname!');</script>";
        } elseif ($is_email_taken) {
            echo "<script>alert('Este e-mail já está cadastrado. Por favor, use outro e-mail ou faça login.');</script>";
        } else {
            // Salvar dados no arquivo de texto
            // Adicionando campos vazios para saldo_moedas, itens_comprados, amigos, solicitacoes_amizade
            $user_data = "$email,$nickname," . password_hash($password, PASSWORD_DEFAULT) . ",0,,,\n";
            file_put_contents($clientes_file, $user_data, FILE_APPEND);

            // Redirecionar para a página de login
            echo "<script>alert('Cadastro realizado com sucesso! Agora você pode fazer login.'); window.location.href='login.php';</script>";
            exit;
        }
    }
    ?>

<?php if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            header('Location: index.php'); // Redirecionar para a página do usuário
        }
        exit;
    } else {
        // Se não encontrar, exibir mensagem de erro
        echo "<script>alert('E-mail ou senha inválidos!');</script>";
    }
}
?>
