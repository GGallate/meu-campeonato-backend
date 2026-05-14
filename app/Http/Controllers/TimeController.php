<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Time;

class TimeController extends Controller
{
    public function cadastrar(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ], [
            'nome.required' => 'O nome do time é obrigatório para o cadastro.'
        ]);

        $quantidadeDeTimes = Time::count();

        if ($quantidadeDeTimes >= 8) {
            return response()->json([
                'erro' => 'O campeonato já possui os 8 times cadastrados.'
            ], 400);
        }

        $novoTime = Time::create([
            'nome' => $request->nome
        ]);

        return response()->json([
            'mensagem' => 'Time cadastrado com sucesso!',
            'time' => $novoTime
        ], 201);
    }

    public function listar()
    {
        $times = Time::all();
        return response()->json($times, 200);
    }
}