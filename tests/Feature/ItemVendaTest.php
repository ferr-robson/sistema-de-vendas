<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\ItemVenda;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemVendaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private int $idInvalido;

    private Array $modelos;
    
    private Array $errosValidacao;
    
    private Array $dadosValidos;

    private Array $dadosInvalidos;

    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->idInvalido = 1000;

        $this->modelos = [
            'produto' => Produto::factory()->create(),
            'itemVenda' => ItemVenda::factory()->create(),
        ];

        $this->errosValidacao = [
            "produto_id" => [
                "The selected produto id is invalid."
            ],
            "quantidade" => [
                "The quantidade field must be at least 1.",
                "The quantidade field must be a number."
            ]
        ];

        $this->dadosInvalidos = [
            'produto_id' => $this->idInvalido,
            'quantidade' => '',
        ];

        $this->dadosValidos = [
            'produto_id' => $this->modelos['produto']->id,
            'quantidade' => 1,
        ];
    }

    public function test_itemvenda_index_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/item-venda');

        $response->assertStatus(200);
    }

    public function test_itemvenda_store_gera_ok_response(): void
    {
        Venda::factory()->create();

        $dados = $this->dadosValidos;
        $dados['venda_id'] = 1;
        
        $response = $this->actingAs($this->user)->postJson('/api/item-venda', $dados);

        $response->assertStatus(201);
        $response->assertJson($dados);
        $this->assertDatabaseHas('item_vendas', $dados);
    }

    public function test_itemvenda_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)
                    ->getJson('/api/item-venda/' . $this->modelos['itemVenda']->id);

        $response->assertStatus(200);
    }

    public function test_itemvenda_update_gera_ok_response_para_atualizacao_de_quantidade(): void
    {
        $dados = $this->dadosValidos;
        $dados['produto_id'] = $this->modelos['itemVenda']->produto_id;
        
        $dadosEsperados = $dados;
        $dadosEsperados['id'] =  $this->modelos['itemVenda']->id;

        $response = $this->actingAs($this->user)
                    ->putJson('/api/item-venda/' . $this->modelos['itemVenda']->id, $dados);

        $response->assertStatus(200);
        $response->assertJson($dadosEsperados);
        $this->assertDatabaseHas('item_vendas', $dadosEsperados);
    }

    public function test_itemvenda_update_gera_ok_response_para_atualizacao_de_produtos(): void
    {
        $dados = $this->dadosValidos;

        $dadosEsperados = $dados;
        $dadosEsperados['id'] = $this->modelos['itemVenda']->id;

        $response = $this->actingAs($this->user)
                    ->putJson('/api/item-venda/' . $this->modelos['itemVenda']->id, $dados);

        $response->assertStatus(200);
        $response->assertJson($dadosEsperados);
        $this->assertDatabaseHas('item_vendas', $dadosEsperados);
    }

    public function test_itemvendastore_captura_ids_e_quantidades_invalidos(): void
    {
        $dados = $this->dadosInvalidos;
        $dados['venda_id'] = $this->idInvalido;

        $response = $this->actingAs($this->user)->postJson('/api/item-venda/', $dados);

        $erros = $this->errosValidacao;
        $erros['venda_id'] = ['The selected venda id is invalid.'];

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($erros);
    }

    public function test_itemvendaupdate_captura_id_e_quantidade_invalidos(): void
    {
        $dados = $this->dadosInvalidos;

        $response = $this->actingAs($this->user)
                    ->putJson('/api/item-venda/' . $this->modelos['itemVenda']->id, $dados);

        $erros = $this->errosValidacao;

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($erros);
    }
}
