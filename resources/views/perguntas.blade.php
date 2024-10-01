<div class="container-fluid form-container">
    <form id="meuForm" method="POST">
        @csrf
        <!-- Navegação das Abas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="secao1-tab" data-toggle="tab" href="#secao1" role="tab" aria-controls="secao1" aria-selected="true">Seção 1</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="secao2-tab" data-toggle="tab" href="#secao2" role="tab" aria-controls="secao2" aria-selected="false">Seção 2</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="secao3-tab" data-toggle="tab" href="#secao3" role="tab" aria-controls="secao3" aria-selected="false">Seção 3</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="secao4-tab" data-toggle="tab" href="#secao4" role="tab" aria-controls="secao4" aria-selected="false">Seção 4</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="secao5-tab" data-toggle="tab" href="#secao5" role="tab" aria-controls="secao5" aria-selected="false">Seção 5</a>
            </li>
        </ul>
        
        <div class="tab-content" id="myTabContent" style="padding: 10px;">
            <div class="tab-pane fade show active" id="secao1" role="tabpanel" aria-labelledby="secao1-tab">
                <h4>Seção 1: Acesso e Usabilidade</h4>
                <div id="lstPerguntas1"></div>
            </div>
    
            <div class="tab-pane fade" id="secao2" role="tabpanel" aria-labelledby="secao2-tab">
                <h4>Seção 2: Compatibilidade com Tecnologias Assistivas</h4>
                <div id="lstPerguntas2"></div>
            </div>
    
            <div class="tab-pane fade" id="secao3" role="tabpanel" aria-labelledby="secao3-tab">
                <h4>Seção 3: Personalização e Adaptação</h4>
                <div id="lstPerguntas3"></div>
            </div>
    
            <div class="tab-pane fade" id="secao4" role="tabpanel" aria-labelledby="secao4-tab">
                <h4>Seção 4: Experiência Geral e Feedback</h4>
                <div id="lstPerguntas4"></div>
            </div>
    
            <div class="tab-pane fade" id="secao5" role="tabpanel" aria-labelledby="secao5-tab">
                <h4>Seção 5: Conhecimento sobre a Legislação</h4>
                <div id="lstPerguntas5"></div>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-primary" id="btnEnviar" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Salvar</button>
            <button type="button" class="btn btn-primary" id="btnCriarPergunta" style="background-color: #0a6d73; border-color: #0a6d73; color: white;">Criar Pergunta</button>
            <br>
        </div>
    </form>
</div>

<div class="modal" id="modalCadPergunta" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p><h5><b>Aguarde, estamos salvando as novas informações</b></h5></p>
                <div class="progress">
                    <div id="loading-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        // Manipulador de clique nas abas
        $('.nav-link').on('click', function (e) {
            let tabId = $(this).attr('id');

            $.ajax({
                url: '/secao',
                type: 'POST',
                data: {
                    tab: tabId,
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    const containerId = 'lstPerguntas' + tabId.substring(5, tabId.length - 4); // Identifica o ID da aba atual
                    const container = document.getElementById(containerId);
                    let innerHTML = '';
                    let cont = 1;

                    if (Array.isArray(response.questions)) {
                        innerHTML = `<div class="form-group" id="${containerId}-conteudo">`; // Contêiner específico da aba

                        for (let i = 0; i < response.questions.length; i++) {
                            const question = response.questions[i];

                            innerHTML += `<label style="font-size: 17px;">
                                            <b>Pergunta ${i+1}:</b> 
                                            <input type="text" class="form-control mt-2" value="${question}">
                                        </label>`;
                            cont++;
                        }

                        innerHTML += `</div>`;
                        sessionStorage.setItem('contPerguntas', cont);
                        container.innerHTML = innerHTML;
                    } else {
                        console.error('A resposta não contém um array de perguntas:', response.questions);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao processar a requisição:', error);
                }
            });
        });

        $('#btnCriarPergunta').click(function() 
        {
            let cont = parseInt(sessionStorage.getItem('contPerguntas') || '1', 10);

            // Obtém o contêiner da aba ativa
            const activeTabId = $('.nav-link.active').attr('id');
            const containerId = '#lstPerguntas' + activeTabId.substring(5, activeTabId.length - 4);

            // Cria a nova pergunta
            let newQuestionHTML = ` <label style="font-size: 17px; display: block;">
                                        <b>Pergunta ${cont}:</b>
                                        <input type="text" class="form-control mt-2" placeholder="Informe a nova pergunta aqui...">
                                    </label>`;

            sessionStorage.setItem('contPerguntas', cont + 1);

            // Adiciona a pergunta ao contêiner correto
            $(containerId).append(newQuestionHTML);
        });

        $('#btnEnviar').click(function() 
        {
            // Obtém o contêiner da aba ativa
            const activeTabId = $('.nav-link.active').attr('id');
            const containerId = '#lstPerguntas' + activeTabId.substring(5, activeTabId.length - 4);

            // Coleta os valores dos inputs da aba ativa
            let values = $(containerId + ' input').map(function(){
                return $(this).val(); // Obtém o valor de cada input
            }).get();

            let activeTabIndex = activeTabId.substring(5, 6); // Identifica o índice da aba ativa

            $.ajax({
                type: 'GET',
                url: '/sucessoPerguntas',
                data: {
                    perguntas: values,
                    abaAtiva: activeTabIndex // Aqui, 'activeTabIndex' deve conter o valor da aba ativa
                },
                success: function(response) {
                    $('#modalCadPergunta').modal('show');
                    var progress = 0;
                    var interval = setInterval(function() {
                        progress += 1;
                        $('#loading-bar').css('width', progress + '%');
                        $('#loading-bar').attr('aria-valuenow', progress);

                        if (progress >= 100) {
                            clearInterval(interval);
                            setTimeout(function() {
                                // Certifique-se de que 'abaAtiva' tenha o valor correto
                                let abaAtiva = activeTabIndex; // Ajuste isso conforme necessário
                                window.location.href = "{{ url('/admin') }}" + "?perguntaInserida=true&abaAtiva=" + abaAtiva;
                            }, 300);
                        }
                    }, 30);

                    console.log(response.message);
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', error);
                }
            });

        });

        //$('#btnFecharPopTelaPerguntas').click(function() {
            //$('#modalCamposPaginaInicial').modal('hide');
        //});


        //$('#modalCamposPaginaInicial').modal('show');
        

        // Ativa a aba inicial
        $('#secao1-tab').trigger('click');
    });
</script>
