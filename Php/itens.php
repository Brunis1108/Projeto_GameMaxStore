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

$usuario_logado = carregarDadosUsuario($_SESSION['email']);
$saldo_moedas = $usuario_logado['saldo_moedas'];
$itens_usuario = $usuario_logado['itens_comprados'];
$amigos_usuario = $usuario_logado['amigos'];

// Mapeamento de nomes de itens para caminhos de imagem (ajuste conforme suas imagens)
$item_images = [
    'Pacote Lendário Hall Of Legends 2025 Uzi' => '../img/pacote_lendario.png',
    'Skin Exclusiva Volibear: Urso dos Mil Flagelos' => '../img/skin.jpg',
    'Kit Jogador Inicial' => '../img/kit_inicial.png',
    'Arma Épica Sakura Vandal' => '../img/arma.jpeg',
    'Emote Raro \'Ok\' Rammus' => '../img/emote.jpeg',
    'Pacote de Skin Luz e Escuridão - Hecarim, Kalista, Yuumi' => '../img/pacote_skin.png',
    'Pacote Lendário' => '../img/pacote_lendario.png', // Para itens comprados antes da alteração do nome
    'Skin Exclusiva' => '../img/skin.jpg', // Para itens comprados antes da alteração do nome
    'Kit Inicial' => '../img/kit_inicial.png', // Para itens comprados antes da alteração do nome
    'Arma Épica' => '../img/arma.jpeg', // Para itens comprados antes da alteração do nome
    'Emote Raro' => '../img/emote.jpeg', // Para itens comprados antes da alteração do nome
    'Pacote de Skin' => '../img/pacote_skin.png', // Para itens comprados antes da alteração do nome
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameMaxStore - Meus Itens</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/itens.css">
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
        <section class="my-items-section container">
            <h2><i class="fas fa-box-open"></i> Meus Itens</h2>
            <?php if (empty($itens_usuario)): ?>
                <p class="no-items-message">Você ainda não possui nenhum item. Visite a <a href="loja.php">Loja</a> para adquirir novos pacotes!</p>
            <?php else: ?>
                <div class="item-grid">
                    <?php foreach ($itens_usuario as $index => $item_data):
                        list($item_nome, $preco_compra) = explode(':', $item_data);
                        $preco_venda = number_format(floatval($preco_compra) / 2, 2, '.', '');
                        $moedas_venda = intval(floatval($preco_compra) / 2 * 100); // Assumindo 1 real = 100 moedas
                        $image_path = $item_images[$item_nome] ?? '../img/default_item.png'; // Imagem padrão
                    ?>
                        <div class="item-card">
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($item_nome) ?>">
                            <div class="item-info">
                                <h3><?= htmlspecialchars($item_nome) ?></h3>
                                <p class="original-price">Preço de Compra: R$ <?= number_format($preco_compra, 2, ',', '.') ?></p>
                                <p class="sell-price">Valor de Venda: <span class="coins-value"><?= number_format($moedas_venda, 0, ',', '.') ?> Moedas</span></p>
                            </div>
                            <div class="item-actions">
                                <button class="btn btn-sell" data-index="<?= $index ?>" data-item-name="<?= htmlspecialchars($item_nome) ?>" data-sell-price="<?= $moedas_venda ?>">Vender</button>
                                <button class="btn btn-gift" data-index="<?= $index ?>" data-item-name="<?= htmlspecialchars($item_nome) ?>">Presentear</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container">
            <p>&copy; 2025 GameMaxStore. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Modal de Confirmação de Venda -->
    <div id="modal-confirmacao-venda" class="modal">
        <div class="modal-content">
            <h2>Confirmar Venda</h2>
            <p id="mensagem-modal-venda"></p>
            <div class="modal-actions">
                <button id="btn-confirmar-venda" class="btn btn-primary">Confirmar</button>
                <button id="btn-cancelar-venda" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Presentear Amigo -->
    <div id="modal-presentear-amigo" class="modal">
        <div class="modal-content">
            <h2>Presentear Amigo</h2>
            <p>Selecione um amigo para presentear com <span id="item-presente-nome"></span>:</p>
            <?php if (empty($amigos_usuario)): ?>
                <p class="no-friends-message">Você não tem amigos para presentear. Adicione amigos na página de <a href="amigos.php">Amigos</a>.</p>
            <?php else: ?>
                <select id="amigo-selecionado">
                    <?php foreach ($amigos_usuario as $amigo): ?>
                        <option value="<?= htmlspecialchars($amigo) ?>"><?= htmlspecialchars($amigo) ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <div class="modal-actions">
                <button id="btn-enviar-presente" class="btn btn-primary" <?= empty($amigos_usuario) ? 'disabled' : '' ?>>Enviar Presente</button>
                <button id="btn-cancelar-presente" class="btn btn-secondary">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        // JavaScript para o menu responsivo
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.main-nav').classList.toggle('active');
        });

        const userCoinsSpan = document.getElementById('user-coins');

        // Lógica para Vender Item
        let itemParaVender = null;
        const modalVenda = document.getElementById('modal-confirmacao-venda');
        const mensagemVenda = document.getElementById('mensagem-modal-venda');
        const btnConfirmarVenda = document.getElementById('btn-confirmar-venda');
        const btnCancelarVenda = document.getElementById('btn-cancelar-venda');

        document.querySelectorAll('.btn-sell').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.dataset.index;
                const itemName = this.dataset.itemName;
                const sellPrice = this.dataset.sellPrice;
                itemParaVender = { index, itemName, sellPrice };

                mensagemVenda.textContent = `Deseja realmente vender "${itemName}" por ${sellPrice} Moedas?`;
                modalVenda.classList.add('active');
            });
        });

        btnConfirmarVenda.addEventListener('click', () => {
            if (itemParaVender) {
                fetch('processar_venda_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        index: itemParaVender.index,
                        item_name: itemParaVender.itemName,
                        sell_price: itemParaVender.sellPrice
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        userCoinsSpan.textContent = data.novo_saldo.toLocaleString('pt-BR');
                        // Remover o card do item vendido
                        const itemCard = document.querySelector(`.item-card .btn-sell[data-index="${itemParaVender.index}"]`).closest('.item-card');
                        if (itemCard) {
                            itemCard.remove();
                        }
                        // Se não houver mais itens, mostrar a mensagem de "nenhum item"
                        if (document.querySelectorAll('.item-card').length === 0) {
                            const itemGrid = document.querySelector('.item-grid');
                            if (itemGrid) {
                                itemGrid.innerHTML = '<p class="no-items-message">Você ainda não possui nenhum item. Visite a <a href="loja.php">Loja</a> para adquirir novos pacotes!</p>';
                            }
                        }
                    }
                    modalVenda.classList.remove('active');
                    itemParaVender = null;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao processar venda.');
                    modalVenda.classList.remove('active');
                    itemParaVender = null;
                });
            }
        });

        btnCancelarVenda.addEventListener('click', () => {
            modalVenda.classList.remove('active');
            itemParaVender = null;
        });

        // Lógica para Presentear Amigo
        let itemParaPresentear = null;
        const modalPresentear = document.getElementById('modal-presentear-amigo');
        const itemPresenteNomeSpan = document.getElementById('item-presente-nome');
        const amigoSelecionadoSelect = document.getElementById('amigo-selecionado');
        const btnEnviarPresente = document.getElementById('btn-enviar-presente');
        const btnCancelarPresente = document.getElementById('btn-cancelar-presente');

        document.querySelectorAll('.btn-gift').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.dataset.index;
                const itemName = this.dataset.itemName;
                itemParaPresentear = { index, itemName };

                itemPresenteNomeSpan.textContent = `"${itemName}"`;
                modalPresentear.classList.add('active');
            });
        });

        btnEnviarPresente.addEventListener('click', () => {
            if (itemParaPresentear && amigoSelecionadoSelect.value) {
                fetch('processar_presente_item.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        index: itemParaPresentear.index,
                        item_name: itemParaPresentear.itemName,
                        amigo_nickname: amigoSelecionadoSelect.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        // Remover o card do item presenteado
                        const itemCard = document.querySelector(`.item-card .btn-gift[data-index="${itemParaPresentear.index}"]`).closest('.item-card');
                        if (itemCard) {
                            itemCard.remove();
                        }
                        // Se não houver mais itens, mostrar a mensagem de "nenhum item"
                        if (document.querySelectorAll('.item-card').length === 0) {
                            const itemGrid = document.querySelector('.item-grid');
                            if (itemGrid) {
                                itemGrid.innerHTML = '<p class="no-items-message">Você ainda não possui nenhum item. Visite a <a href="loja.php">Loja</a> para adquirir novos pacotes!</p>';
                            }
                        }
                    }
                    modalPresentear.classList.remove('active');
                    itemParaPresentear = null;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao enviar presente.');
                    modalPresentear.classList.remove('active');
                    itemParaPresentear = null;
                });
            }
        });

        btnCancelarPresente.addEventListener('click', () => {
            modalPresentear.classList.remove('active');
            itemParaPresentear = null;
        });
    </script>
</body>
</html>
