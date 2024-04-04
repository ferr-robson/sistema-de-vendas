<?php

namespace Database\Factories;

use App\Models\Venda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Parcela>
 */
class ParcelaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {        
        $quantidade = $this->faker->numberBetween(1, 20);

        return [
            'venda_id' => Venda::factory()->create(),
            'data_vencimento' => $this->faker->date(),
            'valor_parcela' => $this->faker->randomFloat(2, 0, 200)
        ];
    }
}
