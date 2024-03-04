<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\FormaPagamento::factory()->create(['nome' => 'Cartão de Crédito']);
        \App\Models\FormaPagamento::factory()->create(['nome' => 'Cartão de Débito']);
        \App\Models\FormaPagamento::factory()->create(['nome' => 'Pix']);
        \App\Models\FormaPagamento::factory()->create(['nome' => 'Boleto']);
    }
}
