<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Gera um PDF a partir de uma view
     */
    public function generatePdf(string $view, array $data = [], array $options = [])
    {
        try {
            // Renderiza a view com os dados
            $html = View::make($view, $data)->render();
            
            // Configurações do PDF
            $filename = $options['filename'] ?? 'relatorio_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            // Gera o PDF usando DomPDF
            $pdf = Pdf::loadHTML($html);
            
            // Configurações opcionais
            if (isset($options['orientation'])) {
                $pdf->setPaper('A4', $options['orientation']);
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Retorna o PDF para download
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            // Em caso de erro, retorna HTML temporariamente para debug
            \Log::error('Erro ao gerar PDF: ' . $e->getMessage());
            
            $html = View::make($view, $data)->render();
            $filename = str_replace('.pdf', '.html', $filename ?? 'relatorio.html');
            
            $headers = [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            
            return response($html, 200, $headers);
        }
    }
    
    /**
     * Gera PDF inline (para visualização no browser)
     */
    public function generatePdfInline(string $view, array $data = [], array $options = [])
    {
        try {
            $html = View::make($view, $data)->render();
            
            // Configurações do PDF
            $pdf = Pdf::loadHTML($html);
            
            if (isset($options['orientation'])) {
                $pdf->setPaper('A4', $options['orientation']);
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Retorna o PDF inline para visualização
            return $pdf->stream();
            
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF inline: ' . $e->getMessage());
            
            $html = View::make($view, $data)->render();
            
            $headers = [
                'Content-Type' => 'text/html',
            ];
            
            return response($html, 200, $headers);
        }
    }
    
    /**
     * Salva PDF em arquivo
     */
    public function savePdf(string $view, array $data = [], string $filepath, array $options = [])
    {
        try {
            $html = View::make($view, $data)->render();
            $pdf = Pdf::loadHTML($html);
            
            if (isset($options['orientation'])) {
                $pdf->setPaper('A4', $options['orientation']);
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            return $pdf->save($filepath);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar PDF: ' . $e->getMessage());
            return false;
        }
    }
}
