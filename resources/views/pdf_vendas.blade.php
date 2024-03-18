<!DOCTYPE html>
<html>
<head>
    <title>Detalhes da Compra</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>Detalhes da Compra #{{$id}}</h1>
<table>
    <thead>
        <tr>
            <th>Id da venda</th>
            <th>Vendedor</th>
            <th>Forma pagamento</th>
            <th>Data da venda</th>
            <th>Valor total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>3</td>
            <td>{{ $vendedor['name'] }}</td>
            <td>{{ $forma_pagamento['nome'] }} {{ $parcelado ? '(Parcelado)' : '(À vista)' }}</td>
            <td>{{ \Carbon\Carbon::parse($data_venda)->format('d/m/Y') }}</td>
            <td>R$ {{ $total_venda }}</td>
        </tr>
    </tbody>
</table>
<h2>Produtos</h2>
<table>
    <thead>
        <tr>
            <th>Produto</th>
            <th>Preço Unitário</th>
            <th>Quantidade</th>
            <th>Valor Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($itens as $item)
        <tr>
            <td>{{ $item['nome'] }}</td>
            <td>R$ {{ number_format($item['preco'], 2, ',', '.') }}</td>
            <td>{{ $item['pivot']['quantidade'] }}</td>
            <td>R$ {{ number_format($item['pivot']['preco'], 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if ($parcelado)
    <h2>Parcelas</h2>
    <table>
        <thead>
            <tr>
                <th>Valor da Parcela</th>
                <th>Data de Vencimento</th>
            </tr>
        </thead>
        <tbody>
            @foreach($parcelas as $parcela)
            <tr>
                <td>R$ {{ number_format($parcela['valor_parcela'], 2, ',', '.') }}</td>
                <td>{{ \Carbon\Carbon::parse($parcela['data_vencimento'])->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

</body>
</html>