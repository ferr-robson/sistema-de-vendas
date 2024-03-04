<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProdutosTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_criar_produto_sem_erros(): void
    {
        $dados = [
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertValid();
    }

    public function test_criar_produto_sem_nome_gera_erro(): void
    {
        $dados = [
            'preco' => 0.5,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_sem_preco_gera_erro(): void
    {
        $dados = [
            'nome' => 'Meu produto',
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_em_texto_gera_erro(): void
    {
        $dados = [
            'nome' => 'Meu produto',
            'preco' => 'abc12.00',
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_menor_do_que_zero_gera_erro(): void
    {
        $dados = [
            'nome' => 'Meu produto',
            'preco' => -1,
        ];

        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_igual_a_zero_gera_erro(): void
    {
        $dados = [
            'nome' => 'Meu produto',
            'preco' => 0,
        ];
        
        $response = $this->post('/api/produto', $dados);

        $response->assertInvalid();
    }
}
