<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
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

$index = intval($_POST['index'] ?? -1);
$item_name = $_POST['item_name'] ?? '';
$sell_price = intval($_POST['sell_price'] ?? 0);

$usuario_logado = carregarDadosUsuario($_SESSION['email']);

if (!$usuario_logado) {
    echo json_encode(['success' => false, 'message' => 'Erro: Usuário não encontrado.']);
    exit;
}

if ($index < 0 || $index >= count($usuario_logado['itens_comprados'])) {
    echo json_encode(['success' => false, 'message' => 'Item inválido.']);
    exit;
}

// Verifica se o item no índice corresponde ao nome e preço esperado (segurança básica)
$item_data_stored = $usuario_logado['itens_comprados'][$index];
list($stored_item_name, $stored_item_price_real) = explode(':', $item_data_stored);

// Recalcula o preço de venda em moedas com base no preço de compra original
$recalculated_sell_price = intval(floatval($stored_item_price_real) / 2 * 100);

if ($stored_item_name !== $item_name || $recalculated_sell_price !== $sell_price) {
    echo json_encode(['success' => false, 'message' => 'Erro de validação do item. Tente novamente.']);
    exit;
}

// Remover o item do inventário
array_splice($usuario_logado['itens_comprados'], $index, 1);

// Adicionar moedas ao saldo
$usuario_logado['saldo_moedas'] += $sell_price;

// Salvar dados atualizados do usuário
salvarDadosUsuario($usuario_logado);

echo json_encode([
    'success' => true,
    'message' => "Item '{$item_name}' vendido com sucesso! Você recebeu {$sell_price} moedas.",
    'novo_saldo' => $usuario_logado['saldo_moedas']
]);
?>
