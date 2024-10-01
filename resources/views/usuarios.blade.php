<div class="container-fluid form-container" id="divCadastro">
    <p><h3>Cadastro de Usuário</h3></p>
    <form id="formUsuario" method="POST">
        @csrf
        <div>
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <div class="form-group">
                <label for="tipo">Tipo:</label>
                <select class="form-control" id="tipo" name="tipo" required>
                    <option value="0">Selecione</option>
                    <option value="1">Admin</option>
                    <option value="2">Respondente</option>
                </select>
            </div>
            <button type="button" id="btnSalvarUsuario" class="btn btn-primary">Salvar</button>
        </div>
    </form>
</div>

<div class="container" id="mensagemSucessoUsuario" style="text-align: center;">
    <div class="mensagem-sucesso">
        <h1>Novo usuário cadastrado com sucesso!!!</h1>
        <p style="font-size: 20px;">Escolha uma das opções no menu acima para dar início ao fluxo desejado conforme seu perfil</p>
    </div>
</div>

<!-- Modal com indicador de erro -->
<div class="modal" id="modalCadUsuario" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><h5><b>Aguarde, estamos salvando as novas informações</b></h5></p>
                <div class="progress">
                    <div id="loading-bar-usuario" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal com indicador de erro -->
<div class="modal" id="modalCamposObrigatoriosUsuario" tabindex="-1" role="dialog" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><h5><b style="color: red;">Verifique, há perguntas não respondidas!!!</b></h5></p>
            </div>
            <div class="modal-body text-center">
                <button type="button" class="btn btn-primary" id="btnFechar">Fechar</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() 
    {
        $('#nome').val();
        $('#senha').val();

        $('#btnSalvarUsuario').click(function() 
        {
            const nomeUsuario = $('#nome').val().trim(); // Remove espaços em branco
            const senhaUsuario = $('#senha').val().trim();
            const tipoUsuario = $('#tipo').val();

            if (nomeUsuario === '' || senhaUsuario === '' || tipoUsuario === '0') {
                $('#modalCamposObrigatoriosUsuario').modal('show');
            }
            else{
                $('#modalCamposObrigatoriosUsuario').modal('hide');
                $.ajax({
                    type: 'GET',
                    url: '/cadastrar-usuario',
                    data: {
                        nome: nomeUsuario,
                        senha: senhaUsuario,
                        tipo: tipoUsuario,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) 
                    {
                        $('#modalCadUsuario').modal('show');
                        var progress = 0;
                        var interval = setInterval(function() {
                            progress += 1;
                            $('#loading-bar-usuario').css('width', progress + '%');
                            $('#loading-bar-usuario').attr('aria-valuenow', progress);

                            if (progress >= 100) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    $('#modalCadUsuario').modal('hide');
                                    $('#divCadastro').css('display', 'none');
                                    $('#mensagemSucessoUsuario').show();
                                }, 300);
                            }
                        }, 30);
                        console.log(response.message);
                    },
                    error: function() {
                        console.error('Erro de ajax');
                    }
                });
            }


        });

        $('#btnFechar').click(function() {
            $('#modalCamposObrigatoriosUsuario').modal('hide');
        });

    });
</script>