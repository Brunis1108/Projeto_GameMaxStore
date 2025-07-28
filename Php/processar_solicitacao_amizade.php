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

$action = $_POST['action'] ?? '';
$solicitante_nickname = $_POST['solicitante'] ?? '';

$usuario_logado = carregarDadosUsuario($_SESSION['email']);

if (!$usuario_logado) {
    echo json_encode(['success' => false, 'message' => 'Erro: Usuário logado não encontrado.']);
    exit;
}

// Remover a solicitação da lista do usuário logado
$usuario_logado['solicitacoes_amizade'] = array_diff($usuario_logado['solicitacoes_amizade'], [$solicitante_nickname]);
$usuario_logado['solicitacoes_amizade'] = array_values($usuario_logado['solicitacoes_amizade']); // Reindexar array

if ($action === 'accept') {
    // Adicionar o solicitante à lista de amigos do usuário logado
    if (!in_array($solicitante_nickname, $usuario_logado['amigos'])) {
        $usuario_logado['amigos'][] = $solicitante_nickname;
    }

    // Adicionar o usuário logado à lista de amigos do solicitante
    $solicitante_dados = encontrarUsuarioPorNickname($solicitante_nickname);
    if ($solicitante_dados) {
        if (!in_array($usuario_logado['nickname'], $solicitante_dados['amigos'])) {
            $solicitante_dados['amigos'][] = $usuario_logado['nickname'];
        }
        salvarDadosUsuario($solicitante_dados);
    }

    salvarDadosUsuario($usuario_logado);
    echo json_encode(['success' => true, 'message' => "Você aceitou a solicitação de amizade de {$solicitante_nickname}."]);

} elseif ($action === 'decline') {
    salvarDadosUsuario($usuario_logado);
    echo json_encode(['success' => true, 'message' => "Você recusou a solicitação de amizade de {$solicitante_nickname}."]);

} else {
    echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
}
?>
