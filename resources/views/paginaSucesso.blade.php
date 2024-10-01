<!DOCTYPE html>
<html lang="pt-br">
<head>
    @include('partials.imports') <!-- Inclui os imports centralizados -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @include('header')

    <div class="container" style="text-align: center;">
        <div class="mensagem-sucesso">
            <h1>Obrigado pela sua participação na pesquisa!</h1>
            <p>Agradecemos pelo tempo dedicado. Sua contribuição é muito importante para nós.</p>
            <button class="btn btn-primary" onclick="fecharJanela()">Fechar</button>
        </div>
    </div>

    <script>

        function fecharJanela() {
            window.location.href = '/login';
        }
    </script>
</body>
</html>
