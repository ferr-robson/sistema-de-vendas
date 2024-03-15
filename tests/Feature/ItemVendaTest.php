<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Produto;
use App\Models\ItemVenda;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemVendaTest extends TestCase
{
    use RefreshDatabase;

    private function criarUserAtor() {
        $user = User::create([
            'name' => 'João',
            'email' => 'joao123@mail.com',
            'password' => 'password'
        ]);

        $this->actingAs($user);
    }  

    public function test_itemvenda_index_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/item-venda');

        $response->assertStatus(200);
    }

    public function test_itemvenda_store_gera_ok_response(): void
    {
        $this->criarUserAtor();

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
            'produtos' => [],
            'qtde_parcelas' => 5
        ]);

        $dados = [
            'produto_id' => 1,
            'venda_id' => 1,
            'quantidade' => 1,
        ];
        
        $response = $this->post('/api/item-venda', $dados);

        $response->assertStatus(201);
    }

    public function test_criar_itemvenda_com_produto_invalido_gera_erro(): void
    {
        $this->criarUserAtor();
        
        User::create([
            'name' => 'Vendedor',
            'email' => 'vendedor@mail.com',
            'password' => 'password'
        ]);

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        Venda::create([
            'cliente_id' => null,
            'forma_pagamento_id' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'vendedor_id' => 1,
            'data_venda' => now(),
            'produtos' => [],
            'qtde_parcelas' => 5
        ]);

        $dados = [
            // o produto de id 1 nao existe
            'produto_id' => 1, 
            'venda_id' => 1,
            'quantidade' => 1,
        ];
        
        $response = $this->post('/api/item-venda', $dados);

        $response->assertInvalid();
    }

    public function test_criar_itemvenda_com_venda_invalida_gera_erro(): void
    {
        $this->criarUserAtor();
        
        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        $dados = [
            'produto_id' => 1, 
            // a venda de id 1 nao existe
            'venda_id' => 1,
            'quantidade' => 1,
        ];
        
        $response = $this->post('/api/item-venda', $dados);

        $response->assertInvalid();
    }

    public function test_itemvenda_show_gera_ok_response(): void
    {
        $this->criarUserAtor();

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
            'produtos' => [],
            'qtde_parcelas' => 5
        ]);

        ItemVenda::create([
            'produto_id' => 1,
            'venda_id' => 1,
            'quantidade' => 1,
            'preco' => 2.55
        ]);
        
        $response = $this->get('/api/item-venda/1');

        $response->assertStatus(200);
    }

    public function test_itemvenda_update_gera_ok_response_para_atualizacao_de_quantidade(): void
    {
        $this->criarUserAtor();

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
                ['produto_id' => 1, 'quantidade' => 4]
            ],
            'qtde_parcelas' => 5
        ]);

        ItemVenda::create([
            'produto_id' => 1,
            'venda_id' => 1,
            'quantidade' => 4,
            'preco' => 2.55
        ]);
        
        $dados = [
            'produto_id' => 1,
            'quantidade' => 1
        ];

        $response = $this->put('/api/item-venda/1', $dados);

        $response->assertStatus(200);
    }

    public function test_itemvenda_update_gera_ok_response_para_atualizacao_de_produtos(): void
    {
        $this->criarUserAtor();

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

        Produto::create([
            'nome' => 'Produto 2',
            'preco' => 5.25
        ]);

        Venda::create([
            'cliente_id' => null,
            'forma_pagamento_id' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'vendedor_id' => 1,
            'data_venda' => now(),
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4]
            ],
            'qtde_parcelas' => 5
        ]);

        ItemVenda::create([
            'produto_id' => 1,
            'venda_id' => 1,
            'quantidade' => 1,
            'preco' => 2.55
        ]);
        
        $dados = [
            'produto_id' => 2,
            'quantidade' => 1
        ];

        $response = $this->put('/api/item-venda/1', $dados);

        $response->assertStatus(200);
    }

    public function test_atualizar_itemvenda_com_produto_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

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
                ['produto_id' => 1, 'quantidade' => 4]
            ],
            'qtde_parcelas' => 5
        ]);

        ItemVenda::create([
            'produto_id' => 1,
            'venda_id' => 1,
            'quantidade' => 1,
            'preco' => 2.55
        ]);
        
        $dados = [
            'produto_id' => 2,
            'quantidade' => 1
        ];

        $response = $this->put('/api/item-venda/1', $dados);

        $response->assertInvalid();
    }
}
