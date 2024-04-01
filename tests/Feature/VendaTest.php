<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Http\Request;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Array $startSetup;

    private Array $dadosVenda;

    private int $idInvalido;

    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->startSetup = [
            'user' => User::factory()->create(),
            'produto' => Produto::factory()->create(),
            'cliente' => '',
            'formaPagamento' => FormaPagamento::factory()->create(),
            'venda' => Venda::factory()->create()
        ];

        $this->dadosVenda = [
            'cliente_id' => null,
            'forma_pagamento_id' => $this->startSetup['formaPagamento']->id,
            'parcelado' => true,
            'produtos' => [
                [
                    'produto_id' => $this->startSetup['produto']->id, 
                    'quantidade' => rand(1,5)
                ],
            ],
            'qtde_parcelas' => rand(1,12)
        ];

        $this->idInvalido = 100;
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
            'forma_pagamento_id' => $this->startSetup['formaPagamento']->id,
            'total_venda' => $this->startSetup['produto']->preco * $this->dadosVenda['produtos'][0]['quantidade'],
            'parcelado' => true,
        ];
        
        $response = $this->actingAs($this->user)->postJson('/api/venda',  $this->dadosVenda);

        $response->assertStatus(201);
        $response->assertJson($dadosEsperados);
        $this->assertDatabaseHas('vendas', $dadosEsperados);
    }

    public function test_criar_venda_com_id_do_cliente_invalido_gera_erro(): void
    {
        $this->VerificarCampoInvalido('cliente_id', Request::METHOD_POST);
    }

    public function test_criar_venda_com_forma_de_pagamento_invalido_gera_erro(): void
    {
        $this->VerificarCampoInvalido('forma_pagamento_id', Request::METHOD_POST);
    }

    public function test_criar_venda_com_produto_nao_existente_gera_erro(): void
    {
        $dados = $this->dadosVenda;
        $dados['produtos'][0]['produto_id'] = $this->idInvalido;

        $response = $this->actingAs($this->user)->postJson('/api/venda',  $dados);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('produtos.0.produto_id');
    }

    public function test_venda_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/venda/1');

        $response->assertStatus(200);
    }

    public function test_atualizar_venda_sem_erros(): void
    {
        $venda = Venda::factory()->create();
        
        $novosDados = [
            'cliente_id' => null,
            'forma_pagamento_id' => 2,
            'parcelado' => false
        ];

        $response = $this->actingAs($this->user)->putJson('/api/venda/' . $venda->id, $novosDados);
        
        $response->assertStatus(200);
        
        $this->assertDatabaseHas('vendas', [
            'id' => $venda->id,
            'cliente_id' => null,
            'vendedor_id' => $venda->vendedor_id,
            'forma_pagamento_id' => 2,
            'data_venda' => $venda->data_venda,
            'total_venda' => $venda->total_venda,
            'parcelado' => false
        ]);
    }

    public function test_atualizar_venda_com_id_do_cliente_invalido_gera_erro(): void
    {
        $this->VerificarCampoInvalido('cliente_id', Request::METHOD_PUT);
    }

    public function test_atualizar_venda_com_forma_de_pagamento_invalido_gera_erro(): void
    {
        $this->VerificarCampoInvalido('forma_pagamento_id', Request::METHOD_PUT);
    }

    public function test_apagar_venda_gera_ok_response(): void
    {
        $venda = Venda::factory()->create();

        $response = $this->actingAs($this->user)->delete('/api/venda/' . $venda->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('vendas', ['id' => $venda->id]);
    }

    private function VerificarCampoInvalido ($campo, $metodo, $id = 1): void
    {
        $dados = $this->dadosVenda;
        $dados[$campo] = $this->idInvalido;

        $response = null;
        switch ($metodo) {
            case Request::METHOD_PUT:
                $response = $this->actingAs($this->user)->putJson('/api/venda/' . $id, $dados);
                break;
            case Request::METHOD_POST:
                $response = $this->actingAs($this->user)->postJson('/api/venda/', $dados);
                break;
            default:
                throw new \InvalidArgumentException("Método HTTP inválido: $metodo");
        }

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($campo);
    }
}
