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

// Função para encontrar usuário por nickname
function encontrarUsuarioPorNickname($nickname) {
    $clientes_file = 'clientes.txt';
    if (file_exists($clientes_file)) {
        $clientes = file($clientes_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($clientes as $cliente) {
            $dados = explode(',', $cliente);
            if ($dados[1] === $nickname) { // Nickname na posição 1
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


$index = intval($_POST['index'] ?? -1);
$item_name = $_POST['item_name'] ?? '';
$amigo_nickname = $_POST['amigo_nickname'] ?? '';

$usuario_remetente = carregarDadosUsuario($_SESSION['email']);

if (!$usuario_remetente) {
    echo json_encode(['success' => false, 'message' => 'Erro: Usuário remetente não encontrado.']);
    exit;
}

if ($index < 0 || $index >= count($usuario_remetente['itens_comprados'])) {
    echo json_encode(['success' => false, 'message' => 'Item inválido.']);
    exit;
}

// Verifica se o item no índice corresponde ao nome (segurança básica)
$item_data_stored = $usuario_remetente['itens_comprados'][$index];
list($stored_item_name, $stored_item_price_real) = explode(':', $item_data_stored);

if ($stored_item_name !== $item_name) {
    echo json_encode(['success' => false, 'message' => 'Erro de validação do item. Tente novamente.']);
    exit;
}

// Encontrar o usuário destinatário
$usuario_destinatario = encontrarUsuarioPorNickname($amigo_nickname);

if (!$usuario_destinatario) {
    echo json_encode(['success' => false, 'message' => 'Amigo não encontrado.']);
    exit;
}

// Verificar se o destinatário é realmente um amigo do remetente
if (!in_array($usuario_destinatario['nickname'], $usuario_remetente['amigos'])) {
    echo json_encode(['success' => false, 'message' => 'Você só pode presentear amigos.']);
    exit;
}

// Remover o item do inventário do remetente
array_splice($usuario_remetente['itens_comprados'], $index, 1);

// Adicionar o item ao inventário do destinatário
$usuario_destinatario['itens_comprados'][] = $item_data_stored;

// Salvar dados atualizados de ambos os usuários
salvarDadosUsuario($usuario_remetente);
salvarDadosUsuario($usuario_destinatario);

// Registrar notificação para o destinatário
$notificacoes_file = 'notificacoes.txt';
$linha_notificacao = $usuario_destinatario['nickname'] . '|' . $usuario_remetente['nickname'] . '|' . $item_name . PHP_EOL;
file_put_contents($notificacoes_file, $linha_notificacao, FILE_APPEND);

echo json_encode([
    'success' => true,
    'message' => "Item '{$item_name}' presenteado para {$amigo_nickname} com sucesso!"
]);
?>
