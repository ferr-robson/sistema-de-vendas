<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_criar_cliente_sem_erros(): void
    {
        $dados = [
            'nome' => 'Nome do Cliente',
            'email' => 'email@cliente.com',
        ];
        
        $response = $this->post('/api/cliente', $dados);

        $response->assertValid();
    }

    public function test_criar_cliente_sem_nome_gera_erro(): void
    {
        $dados = [
            'email' => 'email@cliente.com',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }

    public function test_criar_cliente_sem_email_gera_erro(): void
    {
        $dados = [
            'nome' => 'Nome do Cliente',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }

    public function test_criar_cliente_com_email_invalido_gera_erro(): void
    {
        $dados = [
            'nome' => 'Nome do Cliente',
            'email' => 'emaildocliente',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }
}
