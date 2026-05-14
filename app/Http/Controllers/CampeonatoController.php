<?php

namespace App\Http\Controllers;

use App\Models\Time;
use App\Models\Campeonato;

class CampeonatoController extends Controller
{
    public function simular()
    {
        $times = Time::orderBy('id')->get();

        if ($times->count() !== 8) {
            return response()->json([
                'erro' => 'É necessário ter exatamente 8 times cadastrados para iniciar o campeonato.'
            ], 400);
        }

        $timesArray = $times->toArray();

        $quartas = [];
        $semifinalistas = [];
        for ($i = 0; $i < 8; $i += 2) {
            $partida = $this->jogarPartida($timesArray[$i], $timesArray[$i + 1]);
            $quartas[] = $partida;
            $semifinalistas[] = $partida['vencedor'];
        }

        $semis = [];
        $finalistas = [];
        $disputaTerceiro = [];
        for ($i = 0; $i < 4; $i += 2) {
            $partida = $this->jogarPartida($semifinalistas[$i], $semifinalistas[$i + 1]);
            $semis[] = $partida;
            $finalistas[] = $partida['vencedor'];
            $disputaTerceiro[] = $partida['perdedor'];
        }

        $terceiroLugarPartida = $this->jogarPartida($disputaTerceiro[0], $disputaTerceiro[1]);
        $final = $this->jogarPartida($finalistas[0], $finalistas[1]);

        $campeonatoSalvo = Campeonato::create([
            'campeao' => $final['vencedor']['nome'],
            'vice_campeao' => $final['perdedor']['nome'],
            'terceiro_lugar' => $terceiroLugarPartida['vencedor']['nome'],
            'chaves' => [
                'quartas' => $quartas,
                'semifinais' => $semis,
                'terceiro_lugar' => $terceiroLugarPartida,
                'final' => $final
            ]
        ]);

        return response()->json([
            'mensagem' => 'Campeonato finalizado com sucesso!',
            'resultado' => $campeonatoSalvo
        ], 201);
    }

    private function jogarPartida($timeA, $timeB)
    {
        $caminhoScript = base_path('teste.py');
        
        $resultadoPython = shell_exec("python3 {$caminhoScript}");
        
        $gols = explode("\n", trim($resultadoPython));
        $golsA = isset($gols[0]) ? (int)$gols[0] : 0;
        $golsB = isset($gols[1]) ? (int)$gols[1] : 0;

        if ($golsA === $golsB) {
            if ($timeA['id'] < $timeB['id']) {
                $golsA++;
            } else {
                $golsB++;
            }
        }

        $vencedor = $golsA > $golsB ? $timeA : $timeB;
        $perdedor = $golsA > $golsB ? $timeB : $timeA;

        return [
            'time_a' => $timeA['nome'],
            'time_b' => $timeB['nome'],
            'gols_a' => $golsA,
            'gols_b' => $golsB,
            'vencedor' => $vencedor,
            'perdedor' => $perdedor
        ];
    }

    public function historico()
    {
        $campeonatos = Campeonato::orderBy('created_at', 'desc')->get();
        return response()->json($campeonatos, 200);
    }
}