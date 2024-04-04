<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormaPagamentoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Array $dadosValidos;
    
    private Array $dadosInvalidos;
    
    private Array $errosValidacao;
    
    private Array $modelos;
    
    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->dadosValidos = [
            'nome' => fake()->text(20)
        ];

        $this->dadosInvalidos = [
            'nome' => 'p'
        ];
        
        $this->modelos = [
            'forma_pagamento' => FormaPagamento::factory()->create(),
        ];

        $this->errosValidacao = [
            "nome" => [
                "The nome field must be at least 2 characters."
            ]
        ];
    }

    public function test_formapagamento_index_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/forma-pagamento/');

        $response->assertStatus(200);
    }

    public function test_formapagamento_store_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/forma-pagamento/', $this->dadosValidos);

        $response->assertStatus(201);
        $response->assertJson($this->dadosValidos);
        $this->assertDatabaseHas('forma_pagamentos', $this->dadosValidos);
    }

    public function test_criar_formapagamento_com_nome_menor_do_que_dois_caracteres_gera_erro(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/forma-pagamento/', $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_formapagamento_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)
                    ->getJson('/api/forma-pagamento/' . $this->modelos['forma_pagamento']->id);

        $response->assertStatus(200);
    }

    public function test_formapagamento_update_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)
                ->putJson('/api/forma-pagamento/' . $this->modelos['forma_pagamento']->id, $this->dadosValidos);

        $response->assertStatus(200);
        $this->assertDatabaseHas('forma_pagamentos', $this->dadosValidos);
    }

    public function test_atualizar_formapagamento_com_nome_menor_do_que_dois_caracteres_gera_erro(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/forma-pagamento/' . $this->modelos['forma_pagamento']->id, $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_formapagamento_destroy_gera_ok_response(): void
    {
        $formaPagamento = FormaPagamento::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/forma-pagamento/' . $formaPagamento->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('forma_pagamentos', ['id' => $formaPagamento->id]);
    }
}
