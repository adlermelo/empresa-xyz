<!DOCTYPE html>
<html lang="pt-br">
<head>
    @include('partials.imports') <!-- Inclui os imports centralizados -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @include('header')<br>

    <div class="container" id="msgEntrada" style="text-align: center;">
        <div>
            <h1>Bem-vindo(a) ao Sistema da Empresa XYZ</h1>
            <p style="font-size: 20px;">Escolha uma das opções no menu acima para dar início ao fluxo desejado!</p>
        </div>
    </div>

    <div id="divQuestoes" style="display: none;">
        @include('perguntas')
    </div>

    <div id="divUsuarios" style="display: none;">
        @include('usuarios')
    </div>

    <script>
        $(document).ready(function() 
        {

            

           
            let queryParams = new URLSearchParams(window.location.search);

            // Recupera o valor do parâmetro 'perguntaInserida'
            let perguntaInserida = queryParams.get('perguntaInserida');
            let abaSelecionada = queryParams.get('abaAtiva');

            if(perguntaInserida === 'true'){
                $('#msgEntrada').hide();
                $('#divQuestoes').show();
                $('#secao'+abaSelecionada+'-tab').trigger('click');
            }
            
            $('#btnQuestoes').click(function()
            {
                $('#msgEntrada').css('display', 'none');
                $('#divQuestoes').css('display', 'block');
                $('#divUsuarios').css('display', 'none');
                $('#divCadastro').css('display', 'none');
            });

            $('#btnUsuarios').click(function()
            {
                $('#msgEntrada').css('display', 'none');
                $('#divQuestoes').css('display', 'none');
                $('#divUsuarios').css('display', 'block');
                $('#divCadastro').css('display', 'block');
                $('#mensagemSucessoUsuario').css('display', 'none');
            });
        });
    </script>
</body>
</html>