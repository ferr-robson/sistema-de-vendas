<?php

namespace Database\Factories;

use App\Models\Venda;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemVenda>
 */
class ItemVendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $produto = Produto::factory()->create();
        
        $quantidade = $this->faker->numberBetween(1, 20);

        return [
            'venda_id' => Venda::factory()->create(),
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco' => $produto->preco * $quantidade
        ];
    }
}
