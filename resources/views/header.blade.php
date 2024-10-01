<div class="header-container">
    <div>
        <div class="row form-header">
            <div style="padding-left: 20px; padding-top: 90px; display: flex; flex-direction: column; height: 300px; justify-content: space-between;">
                <div style="align-self: flex-start;">
                    <p id="lblUsuarioPerfil"></p>
                </div>
                <div style="align-self: flex-end; padding-bottom: 90px;" id="divBotoes">
                    <button type="button" class="btn btn-primary" id="btnSair" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Sair</button>
                    <button type="button" class="btn btn-primary" id="btnQuestoes" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Questões</button>
                    <button type="button" class="btn btn-primary" id="btnUsuarios" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Usuários</button>
                </div>
            </div>
            
            <div style="text-align: center; width: 850px;">
                <h2>FORMULÁRIO</h2>
                <h5>Questionário sobre acessibilidade</h5>
            </div>
            <div>
                <img src="{{ asset('logo_empresa.png') }}" alt="Logo da Empresa" style="height: 100px;">
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        const usuario = sessionStorage.getItem('usuario');
        const perfil = sessionStorage.getItem('perfil');
        $('#lblUsuarioPerfil').text(usuario + ' (' + (perfil.charAt(0).toUpperCase() + perfil.slice(1)) + ')');

        if(perfil !== "admin"){
            $('#divBotoes').css('align-self', 'auto'); 
            $('#btnQuestoes').hide();
            $('#btnUsuarios').hide();
        }

        $('#btnSair').click(function(){
            window.location.href = '/login';
        });
    });
</script>

