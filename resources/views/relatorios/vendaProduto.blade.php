<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Vendas do Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .container {
            width: 90%;
            margin: 15px auto;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #28a745; /* Verde */
            color: white;
        }
        tfoot tr:nth-child(2) td {
            background-color: #ff893b; /* Amarelo - Total em Vendas */
        }
        tfoot tr:nth-child(3) td {
            background-color: #add8e6; /* Azul Claro - Total de Impostos */
        }
        tfoot tr:nth-child(4) td {
            background-color: #ffeb3b; /* Amarelo - Total de Frete */
        }
        tfoot tr:nth-child(5) td {
            background-color: #90ee90; /* Verde Claro - Lucro Total */
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Relatório de Vendas - {{ $produto->nome }}</h1>
            <p>Período: {{ now()->subDays(30)->format('d/m/Y') }} - {{ now()->format('d/m/Y') }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Quantidade</th>
                    <th>Compra (R$)</th>
                    <th>Venda (R$)</th>
                    <th>Total Compra (R$)</th>
                    <th>Total Venda (R$)</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtoVendas as $venda)
                <tr>
                    <td>{{ $venda->id }}</td>
                    <td>{{ $produto->nome }}</td>
                    <td>{{ $venda->quantidade }}</td>
                    <td class="text-right">R$ {{ number_format($venda->valor_compra, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format(($venda->valor_compra * $venda->quantidade), 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format(($venda->valor_venda * $venda->quantidade), 2, ',', '.') }}</td>
                    <td>{{ $venda->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right text-bold">Total Venda:</td>
                    <td colspan="4" class="text-right">R$ {{ number_format($produtoVendas->sum('valor_total'), 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right text-bold">Total Imposto:</td>
                    <td colspan="4" class="text-right">R$ {{ number_format($produtoVendas->sum('imposto'), 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right text-bold">Total Frete:</td>
                    <td colspan="4" class="text-right">R$ {{ number_format($produtoVendas->sum('frete'), 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right text-bold">Lucro Total:</td>
                    <td colspan="4" class="text-right">R$ {{ number_format($produtoVendas->sum('valor_total') - ($produtoVendas->sum(function($v) { return $v->valor_compra * $v->quantidade; }) + $produtoVendas->sum('imposto') + $produtoVendas->sum('frete')), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Relatório gerado em {{ now()->format('d/m/Y H:i') }} | Sistema de Vendas</p>
        </div>
    </div>
</body>
</html>
