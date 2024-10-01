<!DOCTYPE html>
<html lang="pt-br">
<head>
    @include('partials.imports') <!-- Inclui os imports centralizados -->
    <link href="{{ asset('css/styleLogin.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="welcome-title">BEM-VINDO</div>
    <input type="text" placeholder="USUÁRIO" class="form-control mb-3" id="username">
    <input type="password" placeholder="SENHA" class="form-control mb-4" id="password">
    <p style="color: yellow; font-size: 20px; display: none;" id="msgErroLoginSenha">Login e/ou Senha inválidos</p>
    <button id="btnEntrar" class="login-button">Entrar</button>
</div>

<!-- Adicionando o script jQuery para redirecionamento -->
<script>
    $(document).ready(function() {
        $('#btnEntrar').on('click', function() {
            var username = $('#username').val();
            var password = $('#password').val();

            $.ajax({
                type: 'POST',
                url: '/verificar-usuario',  // Rota que irá tratar a autenticação
                data: {
                    username: username,
                    password: password
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')  // Adiciona o token CSRF para proteção
                },
                success: function(response) 
                {
                    //sessionStorage.removeItem('usuario');
                    //sessionStorage.removeItem('perfil');
                    sessionStorage.removeItem('additionalColumns');
                    sessionStorage.setItem('usuario', response.usuario);
                    sessionStorage.setItem('perfil', response.perfil);

                    if (response.success && response.perfil === 'admin'){
                        window.location.href = '/admin'; 
                    } 
                    else if (response.success && response.perfil === 'respondente'){
                        sessionStorage.setItem('additionalColumns', JSON.stringify(response.additional_columns));
                        sessionStorage.setItem('perguntas', JSON.stringify(response.perguntas));
                        window.location.href = '/formulario';
                    } 
                    else{
                        $('#msgErroLoginSenha').show();
                    }
                },
                error: function() {
                    console.log('Erro no Ajax');
                }
            });
        });

    });
</script>
</body>
</html>