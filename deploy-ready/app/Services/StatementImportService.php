<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StatementImportService
{
    protected $parsers = [
        'csv' => 'parseCSV',
        'ofx' => 'parseOFX',
        'qif' => 'parseQIF',
    ];

    public function import(UploadedFile $file, BankAccount $bankAccount, string $fileType)
    {
        $method = $this->parsers[$fileType] ?? null;
        
        if (!$method || !method_exists($this, $method)) {
            throw new \Exception("Tipo de arquivo não suportado: {$fileType}");
        }

        $transactions = $this->$method($file);
        
        return $this->processTransactions($transactions, $bankAccount);
    }

    protected function parseCSV(UploadedFile $file)
    {
        $transactions = [];
        $content = $file->get();
        
        // Detectar e converter encoding se necessário
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252']);
        if ($encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        // Remover BOM se existir
        $content = str_replace("\xEF\xBB\xBF", '', $content);
        
        $lines = explode("\n", $content);
        
        // Pegar os headers
        $headers = str_getcsv(array_shift($lines));
        
        // Limpar headers (remover espaços e caracteres especiais)
        $headers = array_map(function($header) {
            return trim($header);
        }, $headers);
        
        // Debug - vamos logar os headers encontrados
        Log::info('CSV Headers encontrados:', $headers);
        
        // Mapear headers
        $headerMap = $this->mapCSVHeaders($headers);
        
        // Debug - vamos logar o mapeamento
        Log::info('Header mapping:', $headerMap);
        
        foreach ($lines as $lineNumber => $line) {
            if (empty(trim($line))) continue;
            
            $data = str_getcsv($line);
            
            // Debug - primeira linha
            if ($lineNumber === 0) {
                Log::info('Primeira linha de dados:', $data);
            }
            
            $transaction = [];
            
            foreach ($headerMap as $field => $index) {
                if ($index !== null && isset($data[$index])) {
                    $transaction[$field] = trim($data[$index]);
                }
            }
            
            if ($this->isValidTransaction($transaction)) {
                $transactions[] = $this->normalizeTransaction($transaction);
            }
        }
        
        Log::info('Total de transações processadas: ' . count($transactions));
        
        return $transactions;
    }

    protected function parseOFX(UploadedFile $file)
    {
        $transactions = [];
        $content = $file->get();
        
        // Parser OFX simplificado
        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/s', $content, $matches);
        
        foreach ($matches[1] as $transactionXml) {
            $transaction = [
                'date' => $this->extractOFXValue($transactionXml, 'DTPOSTED'),
                'amount' => $this->extractOFXValue($transactionXml, 'TRNAMT'),
                'description' => $this->extractOFXValue($transactionXml, 'MEMO'),
                'reference' => $this->extractOFXValue($transactionXml, 'FITID'),
                'type' => $this->extractOFXValue($transactionXml, 'TRNTYPE'),
            ];
            
            if ($this->isValidTransaction($transaction)) {
                $transactions[] = $this->normalizeTransaction($transaction);
            }
        }
        
        return $transactions;
    }

    protected function parseQIF(UploadedFile $file)
    {
        $transactions = [];
        $content = $file->get();
        $lines = explode("\n", $content);
        
        $currentTransaction = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) continue;
            
            if ($line === '^') {
                if (!empty($currentTransaction)) {
                    $transactions[] = $this->normalizeTransaction($currentTransaction);
                    $currentTransaction = [];
                }
                continue;
            }
            
            $type = substr($line, 0, 1);
            $value = substr($line, 1);
            
            switch ($type) {
                case 'D':
                    $currentTransaction['date'] = $value;
                    break;
                case 'T':
                    $currentTransaction['amount'] = $value;
                    break;
                case 'P':
                    $currentTransaction['description'] = $value;
                    break;
                case 'N':
                    $currentTransaction['reference'] = $value;
                    break;
            }
        }
        
        return $transactions;
    }

    protected function processTransactions(array $transactions, BankAccount $bankAccount)
    {
        $result = [
            'total' => count($transactions),
            'imported' => 0,
            'duplicates' => 0,
            'errors' => 0,
            'log' => [],
        ];
        
        foreach ($transactions as $index => $transaction) {
            try {
                $transactionData = [
                    'bank_account_id' => $bankAccount->id,
                    'transaction_date' => $transaction['date'],
                    'description' => $transaction['description'],
                    'amount' => abs($transaction['amount']),
                    'type' => $transaction['type'],
                    'reference_number' => $transaction['reference'] ?? null,
                    'category' => $this->categorizeTransaction($transaction['description']),
                    'status' => 'pending',
                ];
                
                $transactionData['import_hash'] = Transaction::generateImportHash($transactionData);
                
                // Verificar duplicados
                $exists = Transaction::where('bank_account_id', $bankAccount->id)
                    ->where('import_hash', $transactionData['import_hash'])
                    ->exists();
                
                if ($exists) {
                    $result['duplicates']++;
                    $result['log'][] = "Linha {$index}: Transação duplicada";
                } else {
                    Transaction::create($transactionData);
                    $result['imported']++;
                }
                
            } catch (\Exception $e) {
                $result['errors']++;
                $result['log'][] = "Linha {$index}: Erro - {$e->getMessage()}";
            }
        }
        
        return $result;
    }

    protected function mapCSVHeaders(array $headers)
    {
        $mapping = [
            'date' => ['data', 'date', 'dt_transacao', 'transaction_date', 'Data'],
            'description' => ['descricao', 'description', 'memo', 'historico', 'Histórico'],
            'amount' => ['valor', 'amount', 'value', 'montante', 'Valor'],
            'reference' => ['referencia', 'reference', 'ref', 'numero', 'Número do documento'],
        ];
        
        $headerMap = [];
        
        foreach ($mapping as $field => $possibleHeaders) {
            foreach ($headers as $index => $header) {
                $header = trim($header);
                if (in_array($header, $possibleHeaders) || in_array(strtolower($header), array_map('strtolower', $possibleHeaders))) {
                    $headerMap[$field] = $index;
                    break;
                }
            }
        }
        
        // Se não encontrou mapeamento, tentar por posição baseado no arquivo de exemplo
        if (empty($headerMap['date']) && isset($headers[0])) {
            $headerMap['date'] = 0; // Primeira coluna geralmente é data
        }
        if (empty($headerMap['description']) && isset($headers[2])) {
            $headerMap['description'] = 2; // Histórico está na terceira coluna
        }
        if (empty($headerMap['amount']) && isset($headers[5])) {
            $headerMap['amount'] = 5; // Valor está na sexta coluna
        }
        if (empty($headerMap['reference']) && isset($headers[4])) {
            $headerMap['reference'] = 4; // Número do documento
        }
        
        return $headerMap;
    }

    protected function normalizeTransaction(array $transaction)
    {
        // Normalizar data
        if (isset($transaction['date'])) {
            try {
                // Tentar vários formatos de data
                $date = $transaction['date'];
                
                // Se a data estiver no formato dd/mm/yyyy
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
                    $parts = explode('/', $date);
                    $date = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                }
                // Se a data estiver no formato dd/mm/yy
                elseif (preg_match('/^\d{2}\/\d{2}\/\d{2}$/', $date)) {
                    $parts = explode('/', $date);
                    $year = '20' . $parts[2]; // Assumindo século 21
                    $date = $year . '-' . $parts[1] . '-' . $parts[0];
                }
                
                $transaction['date'] = Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error('Erro ao parsear data: ' . $transaction['date']);
                $transaction['date'] = now()->format('Y-m-d');
            }
        }
        
        // Normalizar valor
        if (isset($transaction['amount'])) {
            // Remover espaços e símbolos de moeda
            $amount = trim($transaction['amount']);
            $amount = preg_replace('/[R$\s]/', '', $amount);
            
            // Debug - logar o valor original
            Log::info('Valor original: ' . $amount);
            
            // Detectar o formato do número
            $hasComma = strpos($amount, ',') !== false;
            $hasDot = strpos($amount, '.') !== false;
            
            if ($hasComma && $hasDot) {
                // Determinar qual é o separador decimal
                $commaPos = strrpos($amount, ',');
                $dotPos = strrpos($amount, '.');
                
                if ($commaPos > $dotPos) {
                    // Formato brasileiro: 1.234,56
                    $amount = str_replace('.', '', $amount); // Remove separador de milhares
                    $amount = str_replace(',', '.', $amount); // Converte vírgula decimal para ponto
                } else {
                    // Formato americano: 1,234.56
                    $amount = str_replace(',', '', $amount); // Remove separador de milhares
                }
            } elseif ($hasComma && !$hasDot) {
                // Apenas vírgula - verificar se é decimal ou milhares
                $parts = explode(',', $amount);
                if (count($parts) == 2 && strlen($parts[1]) == 2) {
                    // Provavelmente é decimal (ex: 1223,00)
                    $amount = str_replace(',', '.', $amount);
                } else {
                    // Provavelmente é separador de milhares (ex: 1,223)
                    $amount = str_replace(',', '', $amount);
                }
            } elseif (!$hasComma && $hasDot) {
                // Apenas ponto - verificar se é decimal ou milhares
                $parts = explode('.', $amount);
                if (count($parts) == 2 && strlen($parts[1]) != 2) {
                    // Se a parte depois do ponto não tem 2 dígitos, provavelmente é milhares
                    $amount = str_replace('.', '', $amount);
                }
                // Se tem 2 dígitos após o ponto, assumir que é decimal e manter como está
            }
            
            // Remover qualquer caractere não numérico exceto ponto e sinal negativo
            $amount = preg_replace('/[^0-9\.\-]/', '', $amount);
            
            // Converter para float
            $transaction['amount'] = (float) $amount;
            
            // Debug - logar o valor convertido
            Log::info('Valor convertido: ' . $transaction['amount']);
        }
        
        // Determinar tipo (crédito/débito)
        if (!isset($transaction['type']) && isset($transaction['amount'])) {
            $transaction['type'] = $transaction['amount'] < 0 ? 'debit' : 'credit';
            $transaction['amount'] = abs($transaction['amount']); // Sempre positivo
        }
        
        // Limpar descrição
        if (isset($transaction['description'])) {
            $transaction['description'] = trim($transaction['description']);
        }
        
        // Limpar referência
        if (isset($transaction['reference'])) {
            $transaction['reference'] = trim($transaction['reference']);
        }
        
        return $transaction;
    }

    protected function isValidTransaction(array $transaction)
    {
        return isset($transaction['date']) 
            && isset($transaction['description']) 
            && isset($transaction['amount'])
            && $transaction['amount'] != 0;
    }

    protected function categorizeTransaction(string $description)
    {
        $categories = [
            'Alimentação' => ['restaurante', 'lanchonete', 'padaria', 'mercado', 'supermercado'],
            'Transporte' => ['uber', 'taxi', 'combustivel', 'posto', 'estacionamento'],
            'Saúde' => ['farmacia', 'hospital', 'clinica', 'medico', 'dentista'],
            'Educação' => ['escola', 'universidade', 'curso', 'livro'],
            'Lazer' => ['cinema', 'teatro', 'show', 'viagem'],
            'Moradia' => ['aluguel', 'condominio', 'luz', 'agua', 'gas'],
            'Tecnologia' => ['internet', 'telefone', 'celular', 'software'],
            'Transferência' => ['ted', 'doc', 'pix', 'transferencia'],
        ];
        
        $description = strtolower($description);
        
        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($description, $keyword) !== false) {
                    return $category;
                }
            }
        }
        
        return 'Outros';
    }

    protected function extractOFXValue($xml, $tag)
    {
        preg_match("/<{$tag}>(.*?)</", $xml, $matches);
        return $matches[1] ?? null;
    }
}