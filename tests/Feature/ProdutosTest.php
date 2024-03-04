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
        $response = $this->post('/api/produto', [
            'nome' => 'Meu produto',
            'preco' => 0.5,
        ]);

        $response->assertValid();
    }

    public function test_criar_produto_sem_nome_gera_erro(): void
    {
        $response = $this->post('/api/produto', [
            'preco' => 0.5,
        ]);

        $response->assertInvalid();
    }

    public function test_criar_produto_sem_preco_gera_erro(): void
    {
        $response = $this->post('/api/produto', [
            'nome' => 'Meu produto',
        ]);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_em_texto_gera_erro(): void
    {
        $response = $this->post('/api/produto', [
            'nome' => 'Meu produto',
            'preco' => 'abc12.00',
        ]);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_menor_do_que_zero_gera_erro(): void
    {
        $response = $this->post('/api/produto', [
            'nome' => 'Meu produto',
            'preco' => -1,
        ]);

        $response->assertInvalid();
    }

    public function test_criar_produto_com_preco_igual_a_zero_gera_erro(): void
    {
        $response = $this->post('/api/produto', [
            'nome' => 'Meu produto',
            'preco' => 0,
        ]);

        $response->assertInvalid();
    }
}
