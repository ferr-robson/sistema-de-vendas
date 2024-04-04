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

    private User $user;

    private int $idInvalido;

    private Array $modelos;

    private Array $errosValidacao;

    private Array $dadosValidos;

    private Array $dadosInvalidos;

    protected function setup(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->idInvalido = 1000;

        $this->modelos = [
            'produto' => Produto::factory()->create(),
        ];

        $this->errosValidacao = [
            "nome" => [
                "The nome field is required."
            ],
            "preco" => [
                "The preco field must be a number.",
                "The preco field must be greater than 0."
            ]
        ];

        $this->dadosInvalidos = [
            'preco' => 'x',
        ];

        $this->dadosValidos = [
            'nome' => fake()->text(50),
            'preco' => fake()->randomFloat(2, 1, 200),
        ];
    }

    public function test_produto_index_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/produto');

        $response->assertStatus(200);
    }

    public function test_criar_produto_sem_erros(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/produto', $this->dadosValidos);

        $response->assertStatus(201);
        $this->assertDatabaseHas('produtos', $this->dadosValidos);
    }

    public function test_produto_store_captura_nome_e_valor_invalidos(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/produto', $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_produto_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/produto/' . $this->modelos['produto']->id);

        $response->assertStatus(200);
    }

    public function test_atualizar_produto_sem_erros(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/produto/' . $this->modelos['produto']->id, $this->dadosValidos);

        $response->assertStatus(200);
        $this->assertDatabaseHas('produtos', $this->dadosValidos);
    }

    public function test_produto_update_captura_nome_e_valor_invalidos(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/produto/' . $this->modelos['produto']->id, $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_produto_destroy_gera_ok_response(): void
    {
        $produto = Produto::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/produto/' . $produto->id);

        $response->assertStatus(200);
    }
}
