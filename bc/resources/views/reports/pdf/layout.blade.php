<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Relatório') - BC Sistema</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }
        
        .header h1 {
            margin: 0;
            color: #2563eb;
            font-size: 24px;
        }
        
        .header .subtitle {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .report-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .report-info h3 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            color: #4b5563;
        }
        
        .info-value {
            color: #111827;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .table th,
        .table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .table tr:hover {
            background-color: #f3f4f6;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-success {
            color: #16a34a;
        }
        
        .text-danger {
            color: #dc2626;
        }
        
        .text-warning {
            color: #ca8a04;
        }
        
        .text-info {
            color: #2563eb;
        }
        
        .summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            display: block;
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .summary-value {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #111827;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>@yield('title', 'Relatório')</h1>
        <div class="subtitle">BC Sistema Financeiro</div>
        <div class="subtitle">Gerado em {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

    @yield('content')

    <div class="footer">
        <p>© {{ date('Y') }} BC Sistema - Relatório gerado automaticamente</p>
        <p>Página {{ $page ?? 1 }} de {{ $totalPages ?? 1 }}</p>
    </div>
</body>
</html>
