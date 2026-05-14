<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeController;
use App\Http\Controllers\CampeonatoController;

Route::post('/times', [TimeController::class, 'cadastrar']);
Route::get('/times', [TimeController::class, 'listar']);

Route::post('/campeonatos/simular', [CampeonatoController::class, 'simular']);
Route::get('/campeonatos/', [CampeonatoController::class, 'historico']);