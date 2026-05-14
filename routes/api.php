<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeController;

Route::post('/times', [TimeController::class, 'cadastrar']);
Route::get('/times', [TimeController::class, 'listar']);

Route::post('/campeonato/simular', [App\Http\Controllers\CampeonatoController::class, 'simular']);

Route::get('/campeonatos/', [App\Http\Controllers\CampeonatoController::class, 'historico']);