<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produto;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProdutosTest extends TestCase
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

    public function test_produto_index_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/produto');

        $response->assertStatus(200);
    }

    public function test_criar_produto_sem_erros(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertStatus(201);
    }

    public function test_criar_produto_sem_nome_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'preco' => 0.5,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_sem_preco_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Meu produto',
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_em_texto_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Meu produto',
            'preco' => 'abc12.00',
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_menor_do_que_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Meu produto',
            'preco' => -1,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_igual_a_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Meu produto',
            'preco' => 0,
        ];
        
        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_produto_show_gera_ok_response(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $response = $this->get('/api/produto/1');

        $response->assertStatus(200);
    }

    public function test_atualizar_produto_sem_erros(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $dados = [
            'nome' => 'Meu produto (updated)',
            'preco' => 1.0,
        ];

        $response = $this->put('/api/produto/1', $dados);

        $response->assertStatus(200);
    }

    public function test_atualizar_produto_com_preco_em_texto_gera_erro(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $dados = [
            'nome' => 'Meu produto (updated)',
            'preco' => 'abc1.0',
        ];

        $response = $this->put('/api/produto/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_produto_com_preco_menor_do_que_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $dados = [
            'nome' => 'Meu produto (updated)',
            'preco' => -1,
        ];

        $response = $this->put('/api/produto/1', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_produto_com_preco_igual_a_zero_gera_erro(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $dados = [
            'nome' => 'Meu produto (updated)',
            'preco' => 0,
        ];

        $response = $this->put('/api/produto/1', $dados);

        $response->assertInvalid();
    }

    public function test_produto_destroy_gera_ok_response(): void
    {
        $this->criarUserAtor();

        Produto::create([
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $response = $this->delete('/api/produto/1');

        $response->assertStatus(200);
    }
}
