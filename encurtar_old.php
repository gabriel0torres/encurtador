<?php

header('Content-Type: application/json');

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$response = $_POST['recaptcha-token'] ?? '';
$remoteip = $_SERVER['REMOTE_ADDR'];

$verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$_ENV['SECRET_CAPTCHA']."&response=$response&remoteip=$remoteip");
$captcha_success = json_decode($verify);

if ($captcha_success->success) {
        
    include 'conexao.php';
    
    $url = "https://www.youtube.com/watch?v=Qu0dIn3_2Zc";

    function encurtarUrl($urlOriginal, $Conexao) {

        // Verifica se a URL já foi encurtada anteriormente
        $stmt = $Conexao->prepare("SELECT codigo_encurtado FROM tbl_links where link_original = :urlOriginal");
        $stmt->bindParam(':urlOriginal', $urlOriginal );
        $stmt->execute();
        $codigos = $stmt->fetch(PDO::FETCH_ASSOC);
        $codigos = ($codigos == false ? [] : $codigos); 

        /*if ($urls !== false) {
            return "localhost/$hashExistente";
        }*/

        // Gera uma hash única de 6 caracteres
        do {
            $hash = gerarHash(6);
        } while (array_key_exists($hash, $codigos)); // Garante que não repita

        // Salva no "banco de dados"
        $stmt = $Conexao->prepare("INSERT INTO tbl_links(link_original, codigo_encurtado) VALUES(:urlOriginal, :codigo_encurtado)");
        $stmt->bindParam(':urlOriginal', $urlOriginal );
        $stmt->bindParam(':codigo_encurtado', $hash );
        $stmt->execute();

        // Retorna a URL encurtada
        return "localhost/encurtador/$hash";
    }

    // Função para gerar uma hash aleatória
    function gerarHash($tamanho = 6) {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $hash = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $hash .= $caracteres[random_int(0, strlen($caracteres) - 1)];
        }
        return $hash;
    }

    $short_url = encurtarUrl($url, $Conexao);

    $response = [
        'success' => true,
        'message' => 'Link encurtado com sucesso.',
        'short_url' => "$short_url"
    ];

    echo json_encode($response);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Verificação do reCAPTCHA falhou.'
    ]);
}

