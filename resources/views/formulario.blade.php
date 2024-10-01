<!DOCTYPE html>
<html lang="pt-br">
<head>
    @include('partials.imports') <!-- Inclui os imports centralizados -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

    @include('header')
    
    <br>

    <div class="container" id="msgEntradaPerguntas" style="text-align: center; padding-top: 35px;">
        <div>
            <h1>ATENÇÃO</h1>
            <p style="font-size: 20px;">O presente questionário é uma ação da Empresa XYZ.</p>
            <p style="font-size: 20px;">Tem como objetivo identificar as necessidades específicas das profissionais e </p>
            <p style="font-size: 20px;">dos profissionais sobre a acessibilidade do portal da Empresa XYZ.</p>
            <p style="font-size: 20px;">O preenchimento é voluntário. </p>
            <p style="font-size: 20px;">Dúvidas, entre em contato pelo e-mail: <a href="mailto:empresaxyz@org.br"><b>empresaxyz@org.br</b></a></p>
        </div>
        <button type="button" class="btn btn-primary" id="btnResponder" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Responder</button>
    </div>

    <div class="container-fluid form-container" id="divPrincipal">
        <form id="formFormulario" method="POST">
            @csrf

            @include('pagina_1')
            @include('pagina_2')
            @include('pagina_3')
            @include('pagina_4')
            @include('pagina_5')

        </form>
    </div>

    <!-- Modal com indicador de erro -->
    <div class="modal" id="modalCadQuestoes" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <p><h5><b>Aguarde, estamos salvando as novas informações</b></h5></p>
                    <div class="progress">
                        <div id="loading-bar-questoes" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal com indicador de erro -->
    <div class="modal" id="modalCamposObrigatorios" tabindex="-1" role="dialog">
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
            $('#divPrincipal').css('display', 'none');
            $('#msgEntradaPerguntas').css('display', 'block');

            $('#btnResponder').click(function() {
                $('#divPrincipal').css('display', 'block');
                $('#msgEntradaPerguntas').css('display', 'none');
                updateQuestionsForCurrentPage(1);
                setRadioButtonsFromSession(1);
           });

            const perguntas = JSON.parse(sessionStorage.getItem('perguntas'));
            const additionalColumns = JSON.parse(sessionStorage.getItem('additionalColumns'));
            const totalPages = 5; 
            let currentPage = 1;

            $('#page' + currentPage).addClass('active');

            function updateQuestionsForCurrentPage(paginaAtual){
                const activePage = document.getElementById(`page${paginaAtual}`);
                const container = activePage.querySelector('.containerPerguntas');

                if (container) {
                    container.innerHTML = '';
                    let questionHTML = '';

                    for (let index = 0; index < perguntas[paginaAtual - 1][1][0].length; index++) {
                        questionHTML += `<div class='form-group'>
                                            <label style='font-size: 17px;'><b>${index + 1}.</b> ${perguntas[paginaAtual - 1][1][0][index]}</label>
                                            <div class='form-check form-check-inline' style='font-size: 17px;'>
                                                <input class='form-check-input' type='radio' name='pergunta${paginaAtual}_${index + 1}' id='option1_${paginaAtual}_${index + 1}' value='Sim'>
                                                <label class='form-check-label' for='option1_${paginaAtual}_${index + 1}'>Sim</label>
                                            </div>
                                            <div class='form-check form-check-inline' style='font-size: 17px;'>
                                                <input class='form-check-input' type='radio' name='pergunta${paginaAtual}_${index + 1}' id='option2_${paginaAtual}_${index + 1}' value='Não'>
                                                <label class='form-check-label' for='option2_${paginaAtual}_${index + 1}'>Não</label>
                                            </div>
                                        </div>`;
                    }

                    container.innerHTML += questionHTML;
                }
            }
            
            function setRadioButtonsFromSession(paginaAtual){
                const respostas = perguntas[paginaAtual - 1]?.[1]?.[1] || [];
                const paresInputs = [];
                const activeInputs = $('input:enabled:visible');
                const uniqueNames = new Set();

                activeInputs.each(function () {
                    const nomeInput = $(this).attr('name');
                    if (nomeInput) {
                        uniqueNames.add(nomeInput);
                    }
                });

                const uniqueNamesArray = Array.from(uniqueNames);
                
                for (let i = 0; i < uniqueNamesArray.length; i++) {
                    const nomeInput = uniqueNamesArray[i];
                    const resposta = respostas[i] ?? "";

                    const radios = $(`input[name="${nomeInput}"]`);

                    radios.each(function () {
                        const valorRadio = $(this).val().trim();

                        if (resposta === "Sim" && valorRadio === "Sim") {
                            $(this).prop('checked', true);
                        } else if (resposta === "Não" && valorRadio === "Não") {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                }
            }

            $('.next-btn').click(function() {
                if (currentPage < totalPages) {
                    $('#page' + currentPage).removeClass('active');
                    currentPage++;
                    $('#page' + currentPage).addClass('active');
                    updateQuestionsForCurrentPage(currentPage);
                    setRadioButtonsFromSession(currentPage);
                }
            });

            $('.prev-btn').click(function() {
                if (currentPage > 1) {
                    $('#page' + currentPage).removeClass('active');
                    currentPage--;
                    $('#page' + currentPage).addClass('active');
                    updateQuestionsForCurrentPage(currentPage);
                    setRadioButtonsFromSession(currentPage);
                }
            });

            $('#btnFechar').click(function() {
                $('#modalCamposObrigatorios').modal('hide');
            });

            $('.btnEnviar').click(function (event){
                event.preventDefault();

                $('#modalCadQuestoes').modal('show');

                const usuario = sessionStorage.getItem('usuario');
                let values = recuperaPerguntasRespondidas();

                $.ajax({
                    type: 'POST',
                    url: '/enviar-formulario',
                    data: {
                        tab: currentPage,
                        username: usuario,
                        respostas: values,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response)
                    {
                        var progress = 0;
                        var interval = setInterval(function() {
                            progress += 1;
                            $('#loading-bar-questoes').css('width', progress + '%');
                            $('#loading-bar-questoes').attr('aria-valuenow', progress);

                            if (progress >= 100) {
                                clearInterval(interval);
                                setTimeout(function() {
                                    //$('#modalCadQuestoes').modal('hide');
                                    window.location.href = '/sucessoRespostas';
                                }, 300);
                            }
                        }, 30);
                        console.log(response.message);
                    },
                    error: function (){
                        console.error("Erro ao enviar a requisição");
                    }
                });
            });

            function recuperaPerguntasRespondidas() {
                let values = [];

                $('.containerPerguntas').each(function() {
                    let questionNames = $(this).find('input[type="radio"]:enabled:visible').map(function() {
                        return $(this).attr('name');
                    }).get();

                    questionNames = [...new Set(questionNames)];

                    questionNames.forEach(function(name) {
                        let checkedValue = null;
                        
                        $(this).find(`input[name="${name}"]:enabled:visible`).each(function() {
                            if ($(this).is(':checked')) {
                                checkedValue = $(this).val();
                            }
                        });

                        values.push(checkedValue);
                    }.bind(this));
                });

                return values;
            }
        });
    </script>
</body>
</html>
