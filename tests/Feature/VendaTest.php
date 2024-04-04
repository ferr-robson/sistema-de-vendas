<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Array $modelos;

    private Array $dadosValidos;

    private int $idInvalido;
    
    private Array $dadosInvalidos;
    
    private Array $errosValidacao;

    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->modelos = [
            'user' => User::factory()->create(),
            'produto' => Produto::factory()->create(),
            'formaPagamento' => FormaPagamento::factory()->create(),
            'venda' => Venda::factory()->create()
        ];

        $this->dadosValidos = [
            'cliente_id' => null,
            'forma_pagamento_id' => $this->modelos['formaPagamento']->id,
            'parcelado' => true,
            'produtos' => [
                [
                    'produto_id' => $this->modelos['produto']->id, 
                    'quantidade' => rand(1,5)
                ],
            ],
            'qtde_parcelas' => rand(1,12)
        ];

        $this->idInvalido = 100;

        $this->dadosInvalidos = [
            'cliente_id' => $this->idInvalido,
            'forma_pagamento_id' => $this->idInvalido,
            'parcelado' => true,
            'produtos' => [
                [
                    'produto_id' => $this->idInvalido,
                    'quantidade' => rand(1,5)
                ],
            ],
            'qtde_parcelas' => rand(1,12)
        ];

        $this->errosValidacao = [
            "cliente_id" => [
                "The selected cliente id is invalid."
            ],
            "forma_pagamento_id" => [
                "The selected forma pagamento id is invalid."
            ],
            "produtos.0.produto_id" => [
                "The selected produtos.0.produto_id is invalid."
            ]
        ];
    }

    public function test_venda_index_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/venda/');

        $response->assertStatus(200);
    }
    
    public function test_criar_venda_sem_erros(): void
    {
        $dadosEsperados = [
            'cliente_id' => null,
            'vendedor_id' => 1,
            'forma_pagamento_id' => $this->modelos['formaPagamento']->id,
            'total_venda' => $this->modelos['produto']->preco * $this->dadosValidos['produtos'][0]['quantidade'],
            'parcelado' => true,
        ];
        
        $response = $this->actingAs($this->user)->postJson('/api/venda',  $this->dadosValidos);

        $response->assertStatus(201);
        $response->assertJson($dadosEsperados);
        $this->assertDatabaseHas('vendas', $dadosEsperados);
    }

    public function test_venda_store_captura_ids_invalidos(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/venda/', $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_venda_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/venda/' . $this->modelos['venda']->id);

        $response->assertStatus(200);
    }

    public function test_atualizar_venda_sem_erros(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/venda/' . $this->modelos['venda']->id, $this->dadosValidos);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('vendas', [
            'id' => $this->modelos['venda']->id,
            'cliente_id' => $this->dadosValidos['cliente_id'],
            'forma_pagamento_id' => $this->dadosValidos['forma_pagamento_id'],
            'parcelado' => $this->dadosValidos['parcelado']
        ]);
    }

    public function test_venda_update_captura_ids_invalidos(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/venda/' . $this->modelos['venda']->id, $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_apagar_venda_gera_ok_response(): void
    {
        $venda = Venda::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/venda/' . $venda->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vendas', ['id' => $venda->id]);
    }
}
