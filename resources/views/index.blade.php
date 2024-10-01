<!DOCTYPE html>
<html lang="pt-br">
    <head>
        @include('partials.imports') <!-- Inclui os imports centralizados -->
        <link href="{{ asset('css/layoutindex.css') }}" rel="stylesheet">
    </head>
    
<body>

    <img src="{{ asset('logo_empresa.png') }}" alt="Logo" class="centered-image">
    
    <div>
        <h3>Carregando Sistema!!! Aguarde...</h3>
    </div>  

    <div class="loading-container">
        <div class="progress">
            <div id="loading-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var progress = 0;
            var interval = setInterval(function() {
                progress += 1;
                $('#loading-bar').css('width', progress + '%');
                $('#loading-bar').attr('aria-valuenow', progress);

                if (progress >= 100) {
                    clearInterval(interval);
                    setTimeout(function() {
                        window.location.href = "{{ url('/login') }}";
                    }, 300);
                }
            }, 30);
        });
    </script>
</body>
</html>
