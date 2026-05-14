<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Time;

class CampeonatoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_deve_cadastrar_um_time_com_sucesso()
    {

        $dados = [
            'nome' => 'Irroba Futebol Clube'
        ];

        $response = $this->postJson('/api/times', $dados);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Irroba Futebol Clube']);
                 
        $this->assertDatabaseHas('times', ['nome' => 'Irroba Futebol Clube']);
    }

    public function test_nao_deve_permitir_simulacao_sem_8_times()
    {
        for ($i = 1; $i <= 3; $i++) {
            Time::create([
                'nome' => 'Time Teste ' . $i
            ]);
        }

        $response = $this->postJson('/api/campeonatos/simular');

        $response->assertStatus(400)
                 ->assertJsonFragment(['erro' => 'É necessário ter exatamente 8 times cadastrados para iniciar o campeonato.']);
    }
}