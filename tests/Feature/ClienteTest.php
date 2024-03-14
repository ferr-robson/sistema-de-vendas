<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Cria um usuario a ser utilizado como ator nas acoes de request
     */
    private function criarUserAtor() {
        $user = User::create([
            'name' => 'João',
            'email' => 'joao123@mail.com',
            'password' => 'password'
        ]);

        $this->actingAs($user);
    }    

    public function test_cliente_index_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/cliente/');

        $response->assertValid();
    }

    public function test_criar_cliente_sem_erros(): void
    {
        $this->criarUserAtor();
        
        $dados = [
            'nome' => 'Nome do Cliente',
            'email' => 'email@cliente.com',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertValid();
    }

    public function test_criar_cliente_sem_nome_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'email' => 'email@cliente.com',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }

    public function test_criar_cliente_sem_email_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Nome do Cliente',
        ];
        
        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }

    public function test_criar_cliente_com_email_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        $dados = [
            'nome' => 'Nome do Cliente',
            'email' => 'emaildocliente',
        ];

        $response = $this->post('/api/cliente', $dados);

        $response->assertInvalid();
    }

    public function test_atualizar_cliente_sem_erros(): void
    {
        $this->criarUserAtor();
        
        Cliente::create([
            'nome' => 'Paulo',
            'email' => 'paulo123@email.com'
        ]);
        
        $dados = [
            'nome' => 'Pedro',
            'email' => 'pedro@cliente.com',
        ];

        $response = $this->put('/api/cliente/1', $dados);

        $response->assertValid();
    }

    public function test_atualizar_cliente_com_email_invalido_gera_erro(): void
    {
        $this->criarUserAtor();

        Cliente::create([
            'nome' => 'Paulo',
            'email' => 'paulo123@email.com'
        ]);

        $dados = [
            'email' => 'emaildocliente',
        ];

        $response = $this->put('/api/cliente/1', $dados);

        $response->assertInvalid();
    }

    public function test_cliente_show_gera_ok_response(): void
    {
        $this->criarUserAtor();

        Cliente::create([
            'nome' => 'Paulo',
            'email' => 'paulo123@email.com'
        ]);

        $response = $this->get('/api/cliente/1');

        $response->assertValid();
    }

    public function test_apagar_cliente_gera_ok_response(): void
    {
        $this->criarUserAtor();

        Cliente::create([
            'nome' => 'Paulo',
            'email' => 'paulo123@email.com'
        ]);

        $response = $this->delete('/api/cliente/1');

        $response->assertStatus(200);
    }
}
