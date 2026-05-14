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

        
        $pontuacoes = [];
        foreach ($timesArray as $time) {
            $pontuacoes[$time['id']] = 0;
        }

        $quartas = [];
        $semifinalistas = [];
        for ($i = 0; $i < 8; $i += 2) {
            
            $partida = $this->jogarPartida($timesArray[$i], $timesArray[$i + 1], $pontuacoes);
            $quartas[] = $partida;
            $semifinalistas[] = $partida['vencedor'];
        }

        $semis = [];
        $finalistas = [];
        $disputaTerceiro = [];
        for ($i = 0; $i < 4; $i += 2) {
            $partida = $this->jogarPartida($semifinalistas[$i], $semifinalistas[$i + 1], $pontuacoes);
            $semis[] = $partida;
            $finalistas[] = $partida['vencedor'];
            $disputaTerceiro[] = $partida['perdedor'];
        }

        $terceiroLugarPartida = $this->jogarPartida($disputaTerceiro[0], $disputaTerceiro[1], $pontuacoes);
        $final = $this->jogarPartida($finalistas[0], $finalistas[1], $pontuacoes);

        $campeonatoSalvo = Campeonato::create([
            'campeao' => $final['vencedor']['nome'],
            'vice_campeao' => $final['perdedor']['nome'],
            'terceiro_lugar' => $terceiroLugarPartida['vencedor']['nome'],
            'chaves' => [
                'quartas' => $quartas,
                'semifinais' => $semis,
                'terceiro_lugar' => $terceiroLugarPartida,
                'final' => $final,
                'pontuacao_final_times' => $pontuacoes
            ]
        ]);

        return response()->json([
            'mensagem' => 'Campeonato finalizado com sucesso!',
            'resultado' => $campeonatoSalvo
        ], 201);
    }

    private function jogarPartida($timeA, $timeB, &$pontuacoes)
    {
        $caminhoScript = base_path('teste.py');
        
        $resultadoPython = shell_exec("python3 {$caminhoScript}");
        
        $gols = explode("\n", trim($resultadoPython));
        $golsA = isset($gols[0]) ? (int)$gols[0] : 0;
        $golsB = isset($gols[1]) ? (int)$gols[1] : 0;

        $pontuacoes[$timeA['id']] += ($golsA - $golsB);
        $pontuacoes[$timeB['id']] += ($golsB - $golsA);

        $vencedor = null;
        $perdedor = null;

        if ($golsA > $golsB) {
            $vencedor = $timeA;
            $perdedor = $timeB;
        } elseif ($golsB > $golsA) {
            $vencedor = $timeB;
            $perdedor = $timeA;
        } else {
            if ($pontuacoes[$timeA['id']] > $pontuacoes[$timeB['id']]) {
                $vencedor = $timeA;
                $perdedor = $timeB;
            } elseif ($pontuacoes[$timeB['id']] > $pontuacoes[$timeA['id']]) {
                $vencedor = $timeB;
                $perdedor = $timeA;
            } else {
                if ($timeA['id'] < $timeB['id']) {
                    $vencedor = $timeA;
                    $perdedor = $timeB;
                } else {
                    $vencedor = $timeB;
                    $perdedor = $timeA;
                }
            }
        }

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