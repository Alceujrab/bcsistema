<?php

use App\Services\PdfService;
use Illuminate\Support\Facades\Route;

Route::get('/test-pdf', function () {
    try {
        $pdfService = new PdfService();
        
        $data = [
            'title' => 'Teste de PDF',
            'transactions' => collect([
                (object) [
                    'date' => '2025-06-16',
                    'description' => 'Teste de transação',
                    'type' => 'credit',
                    'amount' => 100.00,
                    'bankAccount' => (object) ['name' => 'Conta Teste']
                ]
            ]),
            'filters' => [
                'date_from' => '2025-06-01',
                'date_to' => '2025-06-16'
            ],
            'summary' => [
                'total_transactions' => 1,
                'total_credit' => 100.00,
                'total_debit' => 0.00,
                'balance' => 100.00
            ]
        ];

        return $pdfService->generatePdf('reports.pdf.transactions', $data, [
            'filename' => 'teste.pdf'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
})->name('test.pdf');
