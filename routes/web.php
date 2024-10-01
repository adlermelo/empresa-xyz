<?php

use App\Http\Controllers\FormularioController;
use Illuminate\Support\Facades\Route;

Route::get('/empresa-xyz', function () {
    return view('index');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/admin', function () {
    return view('telaadmin');
});

Route::get('/formulario', [FormularioController::class, 'index'])->name('formulario.index');
Route::post('/enviar-formulario', [FormularioController::class, 'enviar'])->name('enviar-formulario');
Route::post('/verificar-usuario', [FormularioController::class, 'verificarUsuario'])->name('verificar-usuario');
Route::post('/secao', [FormularioController::class, 'secaoSelecionada'])->name('secao-selecionada');
Route::get('/sucessoPerguntas', [FormularioController::class, 'enviarPerguntas'])->name('enviar-perguntas');
Route::get('/cadastrar-usuario', [FormularioController::class, 'cadastrarUsuario'])->name('cadastrar-usuario');


Route::get('/sucessoRespostas', function () {
    return view('paginaSucesso');
});