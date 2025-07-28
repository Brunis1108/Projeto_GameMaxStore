<?php
session_start();
header('Content-Type: application/json'); // Define o cabeçalho para JSON

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

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$pacote = $_POST['pacote'] ?? '';
$preco_unitario_real = floatval($_POST['preco'] ?? 0);
$moedas_unitario = intval($_POST['moedas'] ?? 0);
$quantidade = intval($_POST['quantidade'] ?? 1); // Nova variável para a quantidade

$email_comprador = $_SESSION['email'];

$usuario_logado = carregarDadosUsuario($email_comprador);

if (!$usuario_logado) {
    echo json_encode(['success' => false, 'message' => 'Erro: Usuário não encontrado.']);
    exit;
}

$total_moedas_necessarias = $moedas_unitario * $quantidade;
$total_preco_real = $preco_unitario_real * $quantidade;

if ($usuario_logado['saldo_moedas'] < $total_moedas_necessarias) {
    echo json_encode(['success' => false, 'message' => 'Moedas insuficientes para esta compra.']);
    exit;
}

// Deduzir moedas do saldo do usuário
$usuario_logado['saldo_moedas'] -= $total_moedas_necessarias;

// Adicionar item(ns) ao inventário do usuário
for ($i = 0; $i < $quantidade; $i++) {
    $usuario_logado['itens_comprados'][] = $pacote . ':' . number_format($preco_unitario_real, 2, '.', ''); // Armazena o preço unitário real
}

// Salvar dados atualizados do usuário
salvarDadosUsuario($usuario_logado);

date_default_timezone_set('America/Sao_Paulo');
// Registrar a compra no arquivo de compras (para relatórios administrativos)
$log_file = 'Banco/compras.txt'; // Certifique-se de que a pasta Banco existe
$data_hora = date('Y-m-d H:i:s');
$log_entry = "[{$data_hora}] Pacote: {$pacote} | Preço: R$ " . number_format($total_preco_real, 2, ',', '.') . " | Quantidade: {$quantidade} | Email: {$email_comprador}" . PHP_EOL;
file_put_contents($log_file, $log_entry, FILE_APPEND);

echo json_encode([
    'success' => true,
    'message' => "Compra de {$quantidade}x '{$pacote}' realizada com sucesso!",
    'novo_saldo' => $usuario_logado['saldo_moedas']
]);
?>
