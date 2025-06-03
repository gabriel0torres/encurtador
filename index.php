<?php 

include 'conexao.php';

$code = $_GET['code'] ?? '';

$stmt = $Conexao->prepare("SELECT link_original FROM tbl_links where codigo_encurtado = :code");
$stmt->bindParam(':code', $code );
$stmt->execute();
$url = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se existe a hash
if (isset($url['link_original'])) {
    header('Location: '.$url['link_original']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encurtador</title>
</head>
<body>

    <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 10%; ">
        <h1>Encurtador Bomba</h1>
    </div>

    <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 90%; ">
        <div style="width: 10%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: aquamarine;">anuncio aqui</div>
        <div style="width: 80%; height: 100%; display: flex; justify-content: center; align-items: center;">
            <form action="encurtar.php" method="post" >
                <label>Insira sua url:</label>
                <input type="text" name="link">
                <div class="g-recaptcha" data-sitekey="6LeHu1QrAAAAAELbrzKH9n8OfwYSsHGOheYwUo5h"></div>
                <button type="submit">ENCURTAR</button>
            </form>
        </div>
        <div style="width: 10%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: aquamarine;">anuncio aqui</div>
    </div>

    

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        body{
            height: 100vh;
            width: 100vw;
            display: flex; 
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin: 0; 
        }
    </style>
    
</body>
</html>