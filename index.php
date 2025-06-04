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
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <title>Encurtador</title>
</head>

<body class="h-screen w-screen flex flex-col justify-center items-center m-0" style="font-family: inter;">

    <div class="flex justify-center items-center w-full h-[10%]">
        <h1 class="text-3xl font-bold">Encurtador Bomba</h1>
    </div>

    <div style="display: flex; justify-content: center; align-items: center; width: 100%; height: 90%; ">
        <div style="width: 10%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: aquamarine;">anuncio aqui</div>
        <div style="width: 80%; height: 100%; display: flex; justify-content: center; align-items: center;">

            <div class="h-[50%] w-[50%] bg-gray-300 flex justify-center items-center rounded-lg p-3">
                <form action="encurtar.php" method="post" class="w-full h-full flex flex-col justify-center items-center gap-4">
                    <label for="link" class="text-2xl font-semibold">INSIRA SUA URL ABAIXO:</label>
                    <input type="text" id="link" name="link" class="block w-full p-4 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-base focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <div class="g-recaptcha" data-sitekey="6LeHu1QrAAAAAELbrzKH9n8OfwYSsHGOheYwUo5h"></div>
                    <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">ENCURTAR</button>
                </form>
            </div>
            
        </div>
        <div style="width: 10%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: aquamarine;">anuncio aqui</div>
    </div>

    

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
</body>
</html>