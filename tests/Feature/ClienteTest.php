<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Array $dadosValidosCliente;
    
    private Array $modelos;
    
    private Array $errosValidacao;

    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->dadosValidosCliente = [
            'nome' => fake()->name(),
            'email' => fake()->email(),
        ];

        $this->modelos = [
            'cliente' => Cliente::factory()->create(),
        ];

        $this->errosValidacao = [
            "email" => [
                "The email field must be a valid email address."
            ]
        ];
    }

    public function test_cliente_index_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/cliente/');

        $response->assertStatus(200);
    }

    public function test_criar_cliente_sem_erros(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/cliente', $this->dadosValidosCliente);

        $response->assertStatus(201);
        $response->assertJson($this->dadosValidosCliente);
        $this->assertDatabaseHas('clientes', $this->dadosValidosCliente);
    }

    public function test_criar_cliente_com_email_invalido_gera_erro(): void
    {
        $dados = $this->dadosValidosCliente;
        $dados['email'] = 'emailinvalido@';

        $response = $this->actingAs($this->user)->postJson('/api/cliente', $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_atualizar_cliente_sem_erros(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/cliente/' . $this->modelos['cliente']->id, $this->dadosValidosCliente);

        $dadosEsperados = $this->dadosValidosCliente;
        $dadosEsperados['id'] = $this->modelos['cliente']->id;
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('clientes', $dadosEsperados);
    }

    public function test_atualizar_cliente_com_email_invalido_gera_erro(): void
    {
        $dados = $this->dadosValidosCliente;
        $dados['email'] = 'emailinvalido@';

        $response = $this->actingAs($this->user)
                        ->putJson('/api/cliente/' . $this->modelos['cliente']->id, $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_cliente_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/cliente/' . $this->modelos['cliente']->id);

        $response->assertStatus(200);
    }

    public function test_apagar_cliente_gera_ok_response(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/cliente/' . $cliente->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vendas', ['id' => $cliente->id]);
    }
}
