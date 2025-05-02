<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Todas as Vendas de Produtos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
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
            background-color: #28a745;
            color: white;
        }
        tfoot tr:nth-child(2) td {
            background-color: #ff893b;
        }
        tfoot tr:nth-child(3) td {
            background-color: #add8e6;
        }
        tfoot tr:nth-child(4) td {
            background-color: #ffeb3b;
        }
        tfoot tr:nth-child(5) td {
            background-color: #90ee90;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Relatório de Todas as Vendas de Produtos</h1>
            <p>Período: {{ $periodo }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Venda</th>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Compra (R$)</th>
                    <th>Venda (R$)</th>
                    <th>Total Compra (R$)</th>
                    <th>Total Venda (R$)</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vendas as $venda)
                <tr>
                    <td>{{ $venda->venda->id ?? 'N/A' }}</td>
                    <td>{{ $venda->produto->nome ?? 'Produto Removido' }}</td>
                    <td>{{ $venda->quantidade }}</td>
                    <td class="text-right">R$ {{ number_format($venda->valor_compra, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($venda->valor_venda, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format(($venda->valor_compra * $venda->quantidade), 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                    <td>{{ $venda->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8"></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right bold">Total em Vendas:</td>
                    <td colspan="3" class="text-right">R$ {{ number_format($totais['faturado'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right bold">Total de Impostos:</td>
                    <td colspan="3" class="text-right">R$ {{ number_format($totais['imposto'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right bold">Total de Frete:</td>
                    <td colspan="3" class="text-right">R$ {{ number_format($totais['frete'], 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right bold">Lucro Total:</td>
                    <td colspan="3" class="text-right">R$ {{ number_format($totais['lucro'], 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Relatório gerado em {{ now()->format('d/m/Y H:i') }} | Sistema de Vendas</p>
        </div>
    </div>
</body>
</html>
