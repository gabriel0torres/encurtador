<?php

require_once 'conexao.php';
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');
http_response_code(200);

session_start();

// Recusar qualquer método que não seja POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// 🔄 Receber e decodificar JSON
$input = json_decode(file_get_contents('php://input'), true);

$url = trim($input['url'] ?? '');
$recaptcha_token = $input['recaptcha_token'] ?? '';

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'URL inválida']);
    exit;
}

if (empty($recaptcha_token)) {
    echo json_encode(['success' => false, 'message' => 'Token reCAPTCHA ausente']);
    exit;
}

// ✅ Verifica o reCAPTCHA v2 (checkbox)
$secret_key = $_ENV['SECRET_CAPTCHA']; // Troque pela sua chave secreta

$recaptcha_response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode($secret_key) . "&response=" . urlencode($recaptcha_token));
$recaptcha_result = json_decode($recaptcha_response, true);

if (!$recaptcha_result['success']) {
    echo json_encode(['success' => false, 'message' => 'Verificação reCAPTCHA falhou']);
    exit;
}

/*
// 🔐 (Opcional) Verifica score ou hostname, se quiser:
if (!empty($recaptcha_result['hostname']) && $recaptcha_result['hostname'] !== 'localhost') {
    echo json_encode(['success' => false, 'message' => 'Requisição suspeita']);
    exit;
}*/

// Função para gerar uma hash aleatória
function gerarHash($tamanho = 6)
{
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $hash = '';
    for ($i = 0; $i < $tamanho; $i++) {
        $hash .= $caracteres[random_int(0, strlen($caracteres) - 1)];
    }
    return $hash;
}

function encurtarUrl($urlOriginal, $Conexao)
{

    // Verifica se a URL já foi encurtada anteriormente
    $stmt = $Conexao->prepare("SELECT codigo_encurtado FROM tbl_links where link_original = :urlOriginal");
    $stmt->bindParam(':urlOriginal', $urlOriginal);
    $stmt->execute();
    $codigos = $stmt->fetch(PDO::FETCH_ASSOC);
    $codigos = ($codigos == false ? [] : $codigos);

    // Gera uma hash única de 6 caracteres
    do {
        $hash = gerarHash(6);
    } while (array_key_exists($hash, $codigos)); // Garante que não repita

    // Salva no "banco de dados"
    $stmt = $Conexao->prepare("INSERT INTO tbl_links(link_original, codigo_encurtado) VALUES(:urlOriginal, :codigo_encurtado)");
    $stmt->bindParam(':urlOriginal', $urlOriginal);
    $stmt->bindParam(':codigo_encurtado', $hash);
    $stmt->execute();

    // Retorna a URL encurtada
    return "localhost/encurtador/$hash";
}

$short_url = encurtarUrl($url, $Conexao);

// 📤 Resposta JSON
echo json_encode([
    'success' => true,
    'message' => 'Link encurtado com sucesso!',
    'short_url' => $short_url
]);
