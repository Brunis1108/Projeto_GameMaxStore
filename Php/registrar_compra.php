<?php
date_default_timezone_set('America/Sao_Paulo');

$pacote = $_POST['pacote'] ?? 'Desconhecido';
$preco = $_POST['preco'] ?? '0.00';
$email = $_POST['email'] ?? '';

$dataHora = date('Y-m-d H:i:s');

$linha = "[$dataHora] Pacote: $pacote | Preço: R$ $preco | Email: " . ($email ?: 'Não informado') . PHP_EOL;

file_put_contents('compras.txt', $linha, FILE_APPEND);

echo "Compra registrada com sucesso!";
?>
