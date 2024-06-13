<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DadosController;
use App\Http\Controllers\RodoviaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/buscar-dados', [DadosController::class, 'buscarDados']);
Route::post('/gravar-rodovia', [DadosController::class, 'gravarDados']);
Route::post('/dados/gravar', [DadosController::class, 'gravarDados'])->name('dados.gravar');

// Trechos definidos
Route::get('/trechos', [DadosController::class, 'listarTrechos']);
Route::delete('/trechos/{id}', [DadosController::class, 'excluirTrecho']);


Route::get('/rodovias', [RodoviaController::class, 'index']);
