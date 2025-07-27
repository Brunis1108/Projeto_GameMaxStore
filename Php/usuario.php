<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$nickname = $_SESSION['nickname'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Meu Perfil</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/usuario.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="usuario.php">GameMaxStore</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="loja.php">Loja</a></li>
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
        <section class="hero-section"> <!-- Mantido o estilo hero-section para a aparência -->
            <div class="container">
                <h1>Bem-vindo, <?php echo htmlspecialchars($nickname); ?>!</h1>
                <p>Você está logado na sua conta.</p>
                <p>Aqui estão seus dados:</p>
                <ul>
                    <li><strong>Nickname:</strong> <?php echo htmlspecialchars($nickname); ?></li>
                    <li><strong>E-mail:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></li>
                </ul>
            </div>
        </section>

        <!-- Conteúdo adicional da página de usuário, se houver -->
        <section class="promotions-section container">
            <h2><i class="fas fa-fire"></i> Sugestões para Você</h2>
            <div class="product-grid">
                <!-- Exemplo de card de item comprado -->
                <div class="product-card">
                    <img src="../img/pacote_lendario.png" alt="Pacote Lendário">
                    <div class="product-info">
                        <h3>Pacote Lendário</h3>
                    </div>
                </div>
                <div class="product-card">
                    <img src="../img/skin.jpg" alt="Skin Exclusiva">
                    <div class="product-info">
                        <h3>Skin Exclusiva</h3>
                    </div>
                </div>
            </div>
        </section>
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
    </script>
</body>
</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pacote = $_POST['pacote'];
    $preco = $_POST['preco'];
    $email = $_POST['email'] ?? $_SESSION['email'];
    $dataHora = date('Y-m-d H:i:s');

    // Aqui você pode adicionar a lógica para registrar a compra no banco de dados
}