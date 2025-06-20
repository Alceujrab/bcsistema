<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use League\Csv\Reader;
use League\Csv\Exception;
use Smalot\PdfParser\Parser as PdfParser;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowImport;

class ExtractImportService
{
    protected $supportedFormats = [
        'csv', 'txt', 'ofx', 'qif', 'pdf', 'xls', 'xlsx'
    ];

    protected $bankPatterns = [
        'itau' => [
            'header_patterns' => ['Data', 'Histórico', 'Valor', 'Saldo'],
            'date_format' => 'd/m/Y',
            'decimal_separator' => ',',
            'thousand_separator' => '.'
        ],
        'bradesco' => [
            'header_patterns' => ['Data', 'Descrição', 'Valor', 'Saldo'],
            'date_format' => 'd/m/Y',
            'decimal_separator' => ',',
            'thousand_separator' => '.'
        ],
        'santander' => [
            'header_patterns' => ['Data', 'Lançamento', 'Valor', 'Saldo'],
            'date_format' => 'd/m/Y',
            'decimal_separator' => ',',
            'thousand_separator' => '.'
        ],
        'caixa' => [
            'header_patterns' => ['Data', 'Histórico', 'Valor', 'Saldo'],
            'date_format' => 'd/m/Y',
            'decimal_separator' => ',',
            'thousand_separator' => '.'
        ],
        'bb' => [
            'header_patterns' => ['Data', 'Histórico', 'Débito', 'Crédito', 'Saldo'],
            'date_format' => 'd/m/Y',
            'decimal_separator' => ',',
            'thousand_separator' => '.'
        ]
    ];

    public function importFile(UploadedFile $file, array $options = [])
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $this->supportedFormats)) {
            return [
                'success' => false,
                'error' => "Formato de arquivo não suportado: {$extension}"
            ];
        }

        // Verificar se as bibliotecas necessárias estão disponíveis
        if (in_array($extension, ['pdf']) && !class_exists('\Smalot\PdfParser\Parser')) {
            return [
                'success' => false,
                'error' => 'Biblioteca PDF Parser não está instalada. Execute: composer require smalot/pdfparser'
            ];
        }

        if (in_array($extension, ['xls', 'xlsx']) && !class_exists('\Maatwebsite\Excel\Facades\Excel')) {
            return [
                'success' => false,
                'error' => 'Biblioteca Excel não está instalada. Execute: composer require maatwebsite/excel'
            ];
        }

        // Detectar banco se não especificado
        if (!isset($options['detected_bank'])) {
            try {
                $content = file_get_contents($file->getRealPath());
                $options['detected_bank'] = $this->detectBank($content);
            } catch (\Exception $e) {
                // Se não conseguir ler o arquivo para detectar banco, continua sem essa informação
                $options['detected_bank'] = null;
            }
        }

        try {
            switch ($extension) {
                case 'csv':
                case 'txt':
                    return $this->importCsv($file, $options);
                case 'ofx':
                    return $this->importOfx($file, $options);
                case 'qif':
                    return $this->importQif($file, $options);
                case 'pdf':
                    return $this->importPdf($file, $options);
                case 'xls':
                case 'xlsx':
                    return $this->importExcel($file, $options);
                default:
                    return [
                        'success' => false,
                        'error' => "Formato não implementado: {$extension}"
                    ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Erro ao processar arquivo {$extension}: " . $e->getMessage()
            ];
        }
    }

    protected function importCsv(UploadedFile $file, array $options)
    {
        try {
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0);
            
            // Detectar encoding
            $encoding = $options['encoding'] ?? $this->detectEncoding($file);
            if ($encoding !== 'UTF-8') {
                $csv->addStreamFilter('convert.iconv.'. $encoding . '/UTF-8');
            }

            // Detectar delimitador
            $delimiter = $options['delimiter'] ?? $this->detectDelimiter($file);
            $csv->setDelimiter($delimiter);

            $records = $csv->getRecords();
            $transactions = [];

            foreach ($records as $record) {
                $transaction = $this->parseTransaction($record, $options);
                if ($transaction) {
                    $transactions[] = $transaction;
                }
            }

            return [
                'success' => true,
                'count' => count($transactions),
                'transactions' => $transactions,
                'format' => 'CSV',
                'encoding' => $encoding,
                'delimiter' => $delimiter,
                'detected_bank' => $options['detected_bank'] ?? null
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao processar arquivo CSV: ' . $e->getMessage()
            ];
        }
    }

    protected function importOfx(UploadedFile $file, array $options)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            
            // Parsing básico de OFX
            $transactions = [];
            
            // Extrair transações usando regex
            preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/s', $content, $matches);
            
            foreach ($matches[1] as $transactionData) {
                $transaction = $this->parseOfxTransaction($transactionData);
                if ($transaction) {
                    $transactions[] = $transaction;
                }
            }

            return [
                'success' => true,
                'count' => count($transactions),
                'transactions' => $transactions,
                'format' => 'OFX',
                'detected_bank' => $options['detected_bank'] ?? null
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao processar arquivo OFX: ' . $e->getMessage()
            ];
        }
    }

    protected function importQif(UploadedFile $file, array $options)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            $lines = explode("\n", $content);
            
            $transactions = [];
            $currentTransaction = [];
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                if (empty($line)) continue;
                
                $field = substr($line, 0, 1);
                $value = substr($line, 1);
                
                switch ($field) {
                    case 'D': // Data
                        $currentTransaction['date'] = $this->parseQifDate($value);
                        break;
                    case 'T': // Valor
                        $currentTransaction['amount'] = $this->parseAmount($value);
                        break;
                    case 'P': // Beneficiário
                        $currentTransaction['payee'] = $value;
                        break;
                    case 'M': // Memo
                        $currentTransaction['memo'] = $value;
                        break;
                    case '^': // Fim da transação
                        if (!empty($currentTransaction)) {
                            $transactions[] = $this->normalizeTransaction($currentTransaction);
                            $currentTransaction = [];
                        }
                        break;
                }
            }

            return [
                'success' => true,
                'count' => count($transactions),
                'transactions' => $transactions,
                'format' => 'QIF'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao processar arquivo QIF: ' . $e->getMessage()
            ];
        }
    }

    protected function importPdf(UploadedFile $file, array $options)
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
            
            // Detectar banco baseado no conteúdo
            $bank = $this->detectBank($text);
            
            // Extrair transações baseado no padrão do banco
            $transactions = $this->extractTransactionsFromText($text, $bank);

            return [
                'success' => true,
                'count' => count($transactions),
                'transactions' => $transactions,
                'format' => 'PDF',
                'detected_bank' => $bank
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao processar arquivo PDF: ' . $e->getMessage()
            ];
        }
    }

    protected function importExcel(UploadedFile $file, array $options)
    {
        try {
            // Usar Maatwebsite Excel para importar
            $data = Excel::toArray([], $file->getRealPath());
            
            if (empty($data) || empty($data[0])) {
                throw new \Exception('Arquivo Excel vazio ou inválido');
            }
            
            $sheet = $data[0]; // Primeira planilha
            $header = array_shift($sheet); // Primeira linha como cabeçalho
            
            // Detectar banco baseado no cabeçalho
            $detectedBank = $this->detectBankFromHeaders($header);
            
            $transactions = [];
            
            foreach ($sheet as $row) {
                if (empty(array_filter($row))) continue; // Pular linhas vazias
                
                $record = array_combine($header, $row);
                $transaction = $this->parseTransaction($record, $options);
                
                if ($transaction) {
                    $transactions[] = $transaction;
                }
            }

            return [
                'success' => true,
                'count' => count($transactions),
                'transactions' => $transactions,
                'format' => 'Excel',
                'detected_bank' => $detectedBank,
                'headers' => $header
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Erro ao processar arquivo Excel: ' . $e->getMessage()
            ];
        }
    }

    protected function detectEncoding(UploadedFile $file)
    {
        $content = file_get_contents($file->getRealPath());
        
        // Lista de encodings possíveis
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($content, $encoding)) {
                return $encoding;
            }
        }
        
        // Fallback para UTF-8
        return 'UTF-8';
    }

    protected function detectDelimiter(UploadedFile $file)
    {
        $content = file_get_contents($file->getRealPath());
        $lines = explode("\n", $content);
        
        if (empty($lines)) return ';';
        
        $firstLine = $lines[0];
        $delimiters = [';', ',', '|', "\t"];
        $delimiterCounts = [];
        
        foreach ($delimiters as $delimiter) {
            $delimiterCounts[$delimiter] = substr_count($firstLine, $delimiter);
        }
        
        // Retorna o delimitador mais comum
        $maxDelimiter = array_keys($delimiterCounts, max($delimiterCounts))[0];
        
        return $maxDelimiter ?: ';';
    }

    protected function detectBankFromHeaders($headers)
    {
        $headers = array_map('strtolower', $headers);
        $headerText = implode(' ', $headers);
        
        foreach ($this->bankPatterns as $bank => $pattern) {
            foreach ($pattern['header_patterns'] as $headerPattern) {
                if (str_contains($headerText, strtolower($headerPattern))) {
                    return $bank;
                }
            }
        }
        
        // Detecção por padrões específicos
        if (str_contains($headerText, 'histórico') && str_contains($headerText, 'saldo')) {
            return 'itau';
        }
        
        if (str_contains($headerText, 'lançamento')) {
            return 'santander';
        }
        
        if (str_contains($headerText, 'débito') && str_contains($headerText, 'crédito')) {
            return 'bb';
        }
        
        return 'unknown';
    }

    protected function detectBank($content)
    {
        $content = strtolower($content);
        
        // Padrões de detecção por conteúdo
        $bankPatterns = [
            'itau' => ['itau', 'itaú', 'banco itau'],
            'bradesco' => ['bradesco', 'banco bradesco'],
            'santander' => ['santander', 'banco santander'],
            'bb' => ['banco do brasil', 'bb', 'bancodobrasil'],
            'caixa' => ['caixa', 'caixa economica', 'cef']
        ];
        
        foreach ($bankPatterns as $bank => $patterns) {
            foreach ($patterns as $pattern) {
                if (str_contains($content, $pattern)) {
                    return $bank;
                }
            }
        }
        
        return 'unknown';
    }

    protected function parseTransaction(array $record, array $options)
    {
        // Implementação básica de parsing
        $transaction = [
            'date' => $this->parseDate($record['Data'] ?? $record['data'] ?? ''),
            'description' => $record['Histórico'] ?? $record['Descrição'] ?? $record['Lançamento'] ?? '',
            'amount' => $this->parseAmount($record['Valor'] ?? ''),
            'balance' => $this->parseAmount($record['Saldo'] ?? ''),
            'type' => $this->determineTransactionType($record)
        ];

        return $transaction['date'] && $transaction['amount'] ? $transaction : null;
    }

    protected function parseOfxTransaction($data)
    {
        $transaction = [];
        
        // Extrair campos OFX
        if (preg_match('/<DTPOSTED>(\d{8})/i', $data, $matches)) {
            $transaction['date'] = \DateTime::createFromFormat('Ymd', $matches[1])->format('Y-m-d');
        }
        
        if (preg_match('/<TRNAMT>([+-]?\d+\.?\d*)/i', $data, $matches)) {
            $transaction['amount'] = floatval($matches[1]);
        }
        
        if (preg_match('/<NAME>(.*?)<\/NAME>/i', $data, $matches)) {
            $transaction['description'] = trim($matches[1]);
        }
        
        return $transaction;
    }

    protected function parseQifDate($dateString)
    {
        // QIF pode usar diferentes formatos de data
        $formats = ['m/d/Y', 'd/m/Y', 'Y-m-d'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) return null;
        
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y'];
        
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }
        
        return null;
    }

    protected function parseAmount($amountString)
    {
        if (empty($amountString)) return 0;
        
        // Remover símbolos de moeda e espaços
        $amount = preg_replace('/[R$\s]/', '', $amountString);
        
        // Converter formato brasileiro (1.234,56) para float
        if (strpos($amount, ',') !== false) {
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        }
        
        return floatval($amount);
    }

    protected function determineTransactionType($record)
    {
        $amount = $this->parseAmount($record['Valor'] ?? '');
        
        if ($amount > 0) {
            return 'credit';
        } elseif ($amount < 0) {
            return 'debit';
        }
        
        // Verificar se há colunas separadas para débito e crédito
        if (isset($record['Débito']) && !empty($record['Débito'])) {
            return 'debit';
        } elseif (isset($record['Crédito']) && !empty($record['Crédito'])) {
            return 'credit';
        }
        
        return 'unknown';
    }

    protected function extractTransactionsFromText($text, $bank)
    {
        $transactions = [];
        
        // Implementação básica de extração de texto
        // Cada banco terá seu próprio padrão
        
        switch ($bank) {
            case 'itau':
                $transactions = $this->extractItauTransactions($text);
                break;
            case 'bradesco':
                $transactions = $this->extractBradescoTransactions($text);
                break;
            default:
                $transactions = $this->extractGenericTransactions($text);
                break;
        }
        
        return $transactions;
    }

    protected function extractItauTransactions($text)
    {
        // Implementação específica para Itaú
        $transactions = [];
        
        // Padrão comum: DD/MM/YYYY DESCRIÇÃO VALOR
        $pattern = '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([+-]?\d{1,3}(?:\.\d{3})*,\d{2})/';
        
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $transactions[] = [
                    'date' => $this->parseDate($match[1]),
                    'description' => trim($match[2]),
                    'amount' => $this->parseAmount($match[3]),
                    'type' => $this->parseAmount($match[3]) >= 0 ? 'credit' : 'debit'
                ];
            }
        }
        
        return $transactions;
    }

    protected function extractBradescoTransactions($text)
    {
        // Implementação específica para Bradesco
        return $this->extractGenericTransactions($text);
    }

    protected function extractGenericTransactions($text)
    {
        // Implementação genérica para quando não conseguimos detectar o banco
        $transactions = [];
        
        // Padrão básico: data + descrição + valor
        $pattern = '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([+-]?\d{1,3}(?:\.\d{3})*,\d{2})/';
        
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $transactions[] = [
                    'date' => $this->parseDate($match[1]),
                    'description' => trim($match[2]),
                    'amount' => $this->parseAmount($match[3]),
                    'type' => $this->parseAmount($match[3]) >= 0 ? 'credit' : 'debit'
                ];
            }
        }
        
        return $transactions;
    }

    protected function normalizeTransaction($transaction)
    {
        return [
            'date' => $transaction['date'] ?? null,
            'description' => $transaction['payee'] ?? $transaction['memo'] ?? 'Transação importada',
            'amount' => $transaction['amount'] ?? 0,
            'type' => $transaction['amount'] >= 0 ? 'credit' : 'debit',
            'memo' => $transaction['memo'] ?? null
        ];
    }

    public function getSupportedFormats()
    {
        return $this->supportedFormats;
    }

    public function getBankPatterns()
    {
        return $this->bankPatterns;
    }
}
