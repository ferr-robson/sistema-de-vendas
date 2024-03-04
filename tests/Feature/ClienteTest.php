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
        $response = $this->post('/api/cliente', [
            'nome' => 'Nome do Cliente',
            'email' => 'email@cliente.com',
        ]);

        $response->assertValid();
    }

    public function test_criar_cliente_sem_nome_gera_erro(): void
    {
        $response = $this->post('/api/cliente', [
            'email' => 'email@cliente.com',
        ]);

        $response->assertInvalid();
    }

    public function test_criar_cliente_sem_email_gera_erro(): void
    {
        $response = $this->post('/api/cliente', [
            'nome' => 'Nome do Cliente',
        ]);

        $response->assertInvalid();
    }

    public function test_criar_cliente_com_email_invalido_gera_erro(): void
    {
        $response = $this->post('/api/cliente', [
            'nome' => 'Nome do Cliente',
            'email' => 'emaildocliente',
        ]);

        $response->assertInvalid();
    }
}
