<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MetaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\ArquivoController;

// Rotas públicas (sem token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (precisam da "pulseirinha")
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('metas', MetaController::class);
    Route::apiResource('eventos', EventoController::class);

    Route::apiResource('livros', LivroController::class);
    Route::get('/lista-leitura', [LivroController::class, 'lista']);
    Route::post('/livros/{livro}/lista', [LivroController::class, 'adicionarNaLista']);
    Route::delete('/livros/{livro}/lista', [LivroController::class, 'removerDaLista']);

    Route::apiResource('documentos', DocumentoController::class);
    Route::get('/arquivos', [ArquivoController::class, 'index']);
    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);
});