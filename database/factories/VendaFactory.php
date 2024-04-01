<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Produto;
use App\Models\FormaPagamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Venda>
 */
class VendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parcelado = $this->faker->randomElement([true, false]);

        return [
            'cliente_id' => null,
            'forma_pagamento_id' => FormaPagamento::factory()->create(),
            'parcelado' => $parcelado,
            'total_venda' => $this->faker->randomFloat(2, 10, 100),
            'vendedor_id' => User::factory()->create(),
            'data_venda' => now()
        ];
    }
}
