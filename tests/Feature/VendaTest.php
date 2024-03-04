<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendaTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_criar_produto_sem_erros(): void
    {
        $dados = [
            'cliente' => 1,
            'forma_pagamento' => 2,
            'total_venda' => 352.25,
            'parcelado' => true,
            'produtos' => [
                ['produto_id' => 1, 'quantidade' => 4],
            ],
            'qtde_parcelas' => 5
        ];

        // $dados = json_encode($dados);
        $response = $this->post('/api/venda',  $dados);

        $response->assertValid();
    }
}
