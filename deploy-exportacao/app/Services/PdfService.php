<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Gera um PDF a partir de uma view
     */
    public function generatePdf(string $view, array $data = [], array $options = [])
    {
        // Renderiza a view com os dados
        $html = View::make($view, $data)->render();
        
        // Por enquanto, vamos retornar como HTML para download
        // Em produção, aqui seria usado o DomPDF ou similar
        $filename = $options['filename'] ?? 'relatorio_' . now()->format('Y-m-d_H-i-s') . '.html';
        
        // Simula a geração de PDF retornando HTML para download
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        return response($html, 200, $headers);
    }
    
    /**
     * Gera PDF inline (para visualização no browser)
     */
    public function generatePdfInline(string $view, array $data = [], array $options = [])
    {
        $html = View::make($view, $data)->render();
        
        $headers = [
            'Content-Type' => 'text/html',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ];
        
        return response($html, 200, $headers);
    }
    
    /**
     * Configurações padrão para PDFs
     */
    protected function getDefaultOptions(): array
    {
        return [
            'format' => 'A4',
            'orientation' => 'portrait',
            'margin' => [
                'top' => 20,
                'right' => 15,
                'bottom' => 20,
                'left' => 15
            ]
        ];
    }
}
