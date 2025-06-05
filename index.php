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
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Prebit - Encurtador de Links</title>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">

  <!-- Anúncio Topo -->
  <div class="w-full max-w-4xl mb-6">
    <div class="bg-yellow-100 border border-yellow-300 text-yellow-700 text-center p-3 rounded-md">
      Espaço para Anúncio no Topo (728x90 ou adaptável)
    </div>
  </div>

  <!-- Header -->
  <header class="mb-6 text-center">
    <h1 class="text-4xl font-bold text-indigo-600">Prebit</h1>
    <p class="text-gray-600 text-lg">Encurte seus links de forma rápida e fácil</p>
  </header>

  <div class="w-full max-w-6xl flex flex-col md:flex-row gap-6">

    <!-- Área Principal -->
    <div class="flex-1 flex flex-col items-center">
      <!-- Formulário -->
      <form id="shortenForm" class="w-full max-w-xl bg-white p-6 rounded-xl shadow-md flex flex-col gap-4" action="encurtar.php" method="POST">
        <input type="url" name="url" id="url" required
          placeholder="Cole sua URL aqui..."
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400" />

        <div class="g-recaptcha " data-sitekey="6LeHu1QrAAAAAELbrzKH9n8OfwYSsHGOheYwUo5h"></div>

        <button type="submit"
          class="bg-indigo-600 text-white px-4 py-3 rounded-lg hover:bg-indigo-700 transition duration-200">
          Encurtar
        </button>
      </form>

      <!-- Resultado -->
      <div id="result" class="mt-6 hidden bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg w-full max-w-xl text-center">
        <strong>Link encurtado:</strong>
        <a href="#" id="shortLink" target="_blank" class="underline break-all"></a>
      </div>

      <!-- Anúncio Rodapé -->
      <div class="w-full max-w-xl mt-8">
        <div class="bg-blue-100 border border-blue-300 text-blue-700 text-center p-3 rounded-md">
          Espaço para Anúncio no Rodapé (responsivo)
        </div>
      </div>
    </div>

    <!-- Anúncio Lateral (visível somente em md+) -->
    <aside class="hidden md:block w-64">
      <div class="bg-red-100 border border-red-300 text-red-700 text-center p-3 rounded-md h-full">
        Espaço para Anúncio Lateral
        <br>
        (visível em telas médias e grandes)
      </div>
    </aside>
  </div>

  <!-- Footer -->
  <footer class="mt-10 text-gray-400 text-sm text-center">
    © 2025 Prebit. Todos os direitos reservados.
  </footer>

  <script src="https://www.google.com/recaptcha/api.js?render=6LeHu1QrAAAAAELbrzKH9n8OfwYSsHGOheYwUo5h"></script>


  <script>
    document.getElementById("shortenForm").addEventListener("submit", function(e) {
      e.preventDefault(); // Evita o envio padrão do form

      const urlInput = document.getElementById("url");
      const url = urlInput.value;

      // Obtém o token do reCAPTCHA v2 (checkbox)
      grecaptcha.ready(function() {
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
          alert("Por favor, verifique o reCAPTCHA.");
          return;
        }
        fetch('encurtar.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              url: url,
              recaptcha_token: recaptchaResponse
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              document.getElementById("result").classList.remove("hidden");
              document.getElementById("shortLink").href = data.short_url;
              document.getElementById("shortLink").textContent = data.short_url;
            } else {
              alert("Erro: " + data.message);
            }
          })
          .catch(error => {
            console.error('Erro ao encurtar:', error);
            alert("Erro inesperado.");
          });
      });
    });
  </script>

</body>

</html>