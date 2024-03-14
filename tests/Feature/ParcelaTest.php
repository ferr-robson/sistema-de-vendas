<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParcelaTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Cria o usar ator, que sera o usuario logado ao fazer a requisicao 
     */
    private function criarUserAtor() {
        $user = User::create([
            'name' => 'João',
            'email' => 'joao123@mail.com',
            'password' => 'password'
        ]);

        $this->actingAs($user);
    }

    /**
     * Cria a venda, que sera usada para associar a parcela
     */
    private function criarVenda() {
        User::create([
            'name' => 'Vendedor',
            'email' => 'vendedor@mail.com',
            'password' => 'password'
        ]);

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        Venda::create([
            'cliente_id' => null,
            'forma_pagamento_id' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'vendedor_id' => 1,
            'data_venda' => now(),
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ]);
    }

    public function test_parcela_index_gera_ok_response_listando_todas_parcelas(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/parcela');

        $response->assertStatus(200);
    }

    public function test_parcela_index_gera_ok_response_para_parcelas_de_uma_venda_especifica(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now(),
            'valor_parcela' => 10.5
        ]);

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            "venda" => 1
        ];

        $response = $this->get('/api/parcela', $dados);

        $response->assertStatus(200);
    }

    public function test_parcela_store_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertStatus(201);
    }

    public function test_criar_parcela_com_vendaid_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_criar_parcela_com_datavenda_anterior_a_atual_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addDays(-1),
            'valor_parcela' => 5.5
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_criar_parcela_com_datavenda_no_formato_incorreto_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => '13/13/2013',
            'valor_parcela' => 5.5
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_criar_parcela_com_valor_nao_numerico_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 'abc5.5'
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_criar_parcela_com_valor_menor_que_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => -1
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_criar_parcela_com_valor_igual_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 0
        ];

        $response = $this->post('/api/parcela', $dados);

        $response->assertInvalid();
    }

    public function test_parcela_show_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $response = $this->get('/api/parcela/1');

        $response->assertStatus(200);
    }

    public function test_parcela_update_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonths(2),
            'valor_parcela' => 10.0
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertStatus(200);
    }

    public function test_atualizar_parcela_com_vendaid_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 2,
            'data_vencimento' => now()->addMonths(2),
            'valor_parcela' => 10.0
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_parcela_com_datavenda_no_formato_incorreto_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => '13/13/2013',
            'valor_parcela' => 5.5
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_parcela_com_valor_nao_numerico_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 'abc5.5'
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_parcela_com_valor_menor_que_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => -1
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_parcela_com_valor_igual_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $dados = [
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 0
        ];

        $response = $this->put('/api/parcela/1', $dados);

        $response->assertInvalid();
    }

    public function test_parcela_destroy_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $this->criarVenda();

        Parcela::create([
            'venda_id' => 1,
            'data_vencimento' => now()->addMonth(),
            'valor_parcela' => 5.5
        ]);

        $response = $this->delete('/api/parcela/1');

        $response->assertStatus(200);
    }
}
