<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FormaPagamento;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormaPagamentoTest extends TestCase
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

    public function test_formapagamento_index_gera_ok_response(): void
    {
        $this->criarUserAtor();

        $response = $this->get('/api/forma-pagamento/');

        $response->assertStatus(200);
    }

    public function test_formapagamento_store_gera_ok_response(): void
    {
        $this->criarUserAtor();
        
        $data = [
            'nome' => 'Boleto'
        ];

        $response = $this->post('/api/forma-pagamento/', $data);

        $response->assertStatus(201);
    }

    public function test_criar_formapagamento_com_nome_menor_do_que_dois_caracteres_gera_erro(): void
    {
        $this->criarUserAtor();
        
        $data = [
            'nome' => 'p'
        ];

        $response = $this->post('/api/forma-pagamento/', $data);

        $response->assertInvalid();
    }

    public function test_formapagamento_show_gera_ok_response(): void
    {
        $this->criarUserAtor();

        FormaPagamento::create([
            'nome' => 'Boleto'
        ]);

        $response = $this->get('/api/forma-pagamento/1');

        $response->assertStatus(200);
    }

    public function test_formapagamento_update_gera_ok_response(): void
    {
        $this->criarUserAtor();
        
        FormaPagamento::create([
            'nome' => 'Boleto'
        ]);

        $data = [
            'nome' => 'Cartão de Crédito'
        ];

        $response = $this->put('/api/forma-pagamento/1', $data);

        $response->assertStatus(200);
    }

    public function test_atualizar_formapagamento_com_nome_menor_do_que_dois_caracteres_gera_erro(): void
    {
        $this->criarUserAtor();
        
        FormaPagamento::create([
            'nome' => 'Boleto'
        ]);

        $data = [
            'nome' => 'p'
        ];

        $response = $this->put('/api/forma-pagamento/1', $data);

        $response->assertInvalid();
    }

    public function test_formapagamento_destroy_gera_ok_response(): void
    {
        $this->criarUserAtor();
        
        FormaPagamento::create([
            'nome' => 'Boleto'
        ]);

        $response = $this->delete('/api/forma-pagamento/1');

        $response->assertStatus(200);
    }
}
