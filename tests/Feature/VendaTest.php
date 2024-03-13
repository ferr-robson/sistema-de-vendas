<?php

namespace Tests\Feature;

use App\Models\FormaPagamento;
use App\Models\Produto;
use App\Models\Venda;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cria um usuario a ser utilizado como ator nas acoes de request
     */
    private function criarUserAtor(){
        $user = User::create([
            'name' => 'João',
            'email' => 'joao123@mail.com',
            'password' => 'password'
        ]);

        $this->actingAs($user);
    }

    public function test_venda_index_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/venda/');

        $response->assertValid();
    }
    
    public function test_criar_venda_sem_erros(): void
    {
        $this->criarUserAtor();

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        $dados = [
            'cliente' => null,
            'forma_pagamento' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        $response = $this->post('/api/venda',  $dados);

        $response->assertValid();
    }

    public function test_criar_venda_com_id_do_cliente_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        $dados = [
            'cliente' => 1,
            'forma_pagamento' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        $response = $this->post('/api/venda',  $dados);

        $response->assertInvalid();
    }

    public function test_criar_venda_com_forma_de_pagamento_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        $dados = [
            'cliente' => null,
            'forma_pagamento' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        $response = $this->post('/api/venda',  $dados);

        $response->assertInvalid();
    }

    public function test_criar_venda_com_totalvenda_incorreto_gera_erro(): void
    {
        $this->criarUserAtor();

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        Produto::create([
            'nome' => 'Produto 1',
            'preco' => 2.55
        ]);

        $dados = [
            'cliente' => null,
            'forma_pagamento' => 1,
            'total_venda' => 10.3,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        $response = $this->post('/api/venda',  $dados);

        $response->assertStatus(400);
    }

    public function test_criar_venda_com_produto_nao_existente_gera_erro(): void
    {
        $this->criarUserAtor();

        FormaPagamento::create([
            'nome' => 'Pix'
        ]);

        $dados = [
            'cliente' => null,
            'forma_pagamento' => 1,
            'total_venda' => 10.2,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        $response = $this->post('/api/venda',  $dados);

        $response->assertInvalid();
    }

    public function test_venda_show_gera_ok_response(): void
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
            'cliente' => null,
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

        $response = $this->get('/api/venda/1');

        $response->assertValid();
    }

    public function test_atualizar_venda_sem_erros(): void
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
            'cliente' => null,
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

        $dados = [
            'cliente_id' => null,
            'forma_pagamento_id' => 1,
            'total_venda' => 2.55,
            'parcelado' => false,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 1],
            ],
        ];

        $response = $this->put('/api/venda/1', $dados);

        $response->assertValid();
    }

    public function test_atualizar_venda_com_id_do_cliente_invalido_gera_erro(): void
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
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ]);

        $dados = [
            'cliente_id' => 1,
            'forma_pagamento_id' => 1,
            'total_venda' => 2.55,
            'parcelado' => false,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 1],
            ],
        ];

        $response = $this->put('/api/venda/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_venda_com_forma_de_pagamento_invalido_gera_erro(): void
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
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ]);

        $dados = [
            'cliente_id' => null,
            'forma_pagamento_id' => 2,
            'total_venda' => 2.55,
            'parcelado' => false,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 1],
            ],
        ];

        $response = $this->put('/api/venda/1', $dados);

        $response->assertInvalid();
    }

    public function test_apagar_venda_gera_ok_response(): void
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
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ]);

        $response = $this->delete('/api/venda/1');

        $response->assertStatus(200);
    }
}
