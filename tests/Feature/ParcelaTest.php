<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Venda;
use App\Models\Parcela;
use App\Models\Produto;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParcelaTest extends TestCase
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
            'venda' => Venda::factory()->create(),
            'parcela' => Parcela::factory()->create(),
        ];

        $this->errosValidacao = [
            "venda_id" => [
                "The selected venda id is invalid."
            ],
            "valor_parcela" => [
                "The valor parcela field must be at least 1.",
                "The valor parcela field must be a number."
            ],
            "data_vencimento" => [
                "The data vencimento field must be a valid date."
            ],
        ];

        $this->dadosInvalidos = [
            'venda_id' => $this->idInvalido,
            'data_vencimento' => '09/2009',
            'valor_parcela' => 'x'
        ];

        $this->dadosValidos = [
            'venda_id' => $this->modelos['venda']->id,
            'data_vencimento' => $this->modelos['venda']->data_venda,
            'valor_parcela' => fake()->randomFloat(2, 1, 200),
        ];
    }
    
    public function test_parcela_index_gera_ok_response_listando_todas_parcelas(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/parcela');

        $response->assertStatus(200);
    }

    public function test_parcela_index_gera_ok_response_para_parcelas_de_uma_venda_especifica(): void
    {
        $parcela = Parcela::factory()->create();

        $response = $this->actingAs($this->user)->getJson('/api/parcela', ['venda' => $parcela->venda_id]);

        $response->assertStatus(200);
    }

    public function test_parcela_store_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/parcela', $this->dadosValidos);

        $response->assertStatus(201);
        $this->assertDatabaseHas('parcelas', [
            'venda_id' => $this->modelos['venda']->id,
            'valor_parcela' => $this->dadosValidos['valor_parcela'],
        ]);
    }

    public function test_parcela_show_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/parcela/' . $this->modelos['parcela']->id);

        $response->assertStatus(200);
    }

    public function test_parcela_update_gera_ok_response(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/parcela/' . $this->modelos['parcela']->id, $this->dadosValidos);
        
        $response->assertStatus(200);
        $this->assertDatabaseHas('parcelas', [
            'id' => $this->modelos['parcela']->id, 
            'valor_parcela' => $this->dadosValidos['valor_parcela']
        ]);
    }

    public function test_parcela_update_captura_id_datavencimento_e_valor_invalidos(): void
    {
        $response = $this->actingAs($this->user)
                    ->putJson('/api/parcela/' . $this->modelos['parcela']->id, $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_parcela_store_captura_id_datavencimento_e_valor_invalidos(): void
    {
        $response = $this->actingAs($this->user)->postJson('/api/parcela/', $this->dadosInvalidos);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($this->errosValidacao);
    }

    public function test_parcela_destroy_gera_ok_response(): void
    {
        $parcela = Parcela::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson('/api/parcela/' . $parcela->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('parcelas', ['id' => $parcela->id]);
    }
}
