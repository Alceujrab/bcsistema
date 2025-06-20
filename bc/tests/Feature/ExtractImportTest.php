<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Services\ExtractImportService;
use App\Models\BankAccount;

class ExtractImportTest extends TestCase
{
    use RefreshDatabase;

    protected $importService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->importService = app(ExtractImportService::class);
        Storage::fake('local');
    }

    /** @test */
    public function it_can_detect_supported_formats()
    {
        $formats = $this->importService->getSupportedFormats();
        
        $this->assertContains('csv', $formats);
        $this->assertContains('ofx', $formats);
        $this->assertContains('qif', $formats);
        $this->assertContains('pdf', $formats);
        $this->assertContains('xlsx', $formats);
    }

    /** @test */
    public function it_can_import_csv_file()
    {
        // Criar arquivo CSV de teste
        $csvContent = "Data,Descrição,Valor,Saldo\n";
        $csvContent .= "15/06/2025,\"Pagamento PIX\",\"-150,00\",\"2850,00\"\n";
        $csvContent .= "16/06/2025,\"Transferência recebida\",\"500,00\",\"3350,00\"\n";
        
        $file = UploadedFile::fake()->createWithContent('extrato.csv', $csvContent);
        
        $result = $this->importService->importFile($file, [
            'encoding' => 'UTF-8',
            'delimiter' => ',',
            'date_format' => 'd/m/Y'
        ]);
        
        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['count']);
        $this->assertEquals('CSV', $result['format']);
    }

    /** @test */
    public function it_can_detect_bank_from_csv_content()
    {
        // CSV do Itaú
        $csvContent = "Data,Histórico,Valor,Saldo\n";
        $csvContent .= "15/06/2025,\"Pagamento PIX\",\"-150,00\",\"2850,00\"\n";
        
        $file = UploadedFile::fake()->createWithContent('extrato_itau.csv', $csvContent);
        
        $result = $this->importService->importFile($file);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('itau', $result['detected_bank'] ?? null);
    }

    /** @test */
    public function it_handles_different_encodings()
    {
        // Simular arquivo com encoding ISO-8859-1
        $csvContent = "Data;Descrição;Valor\n";
        $csvContent .= "15/06/2025;Pagamento;-150,00\n";
        
        $file = UploadedFile::fake()->createWithContent('extrato_iso.csv', $csvContent);
        
        $result = $this->importService->importFile($file);
        
        $this->assertTrue($result['success']);
    }

    /** @test */
    public function it_parses_brazilian_amounts_correctly()
    {
        $testCases = [
            '1.234,56' => 1234.56,
            '-500,00' => -500.00,
            'R$ 2.000,00' => 2000.00,
            '1,50' => 1.50,
            '-1.500,75' => -1500.75
        ];

        $reflection = new \ReflectionClass($this->importService);
        $method = $reflection->getMethod('parseAmount');
        $method->setAccessible(true);

        foreach ($testCases as $input => $expected) {
            $result = $method->invokeArgs($this->importService, [$input]);
            $this->assertEquals($expected, $result, "Failed parsing: {$input}");
        }
    }

    /** @test */
    public function it_handles_invalid_file_formats()
    {
        $file = UploadedFile::fake()->create('document.doc', 100);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Formato de arquivo não suportado: doc');
        
        $this->importService->importFile($file);
    }

    /** @test */
    public function it_can_process_ofx_file()
    {
        $ofxContent = '<?xml version="1.0" encoding="UTF-8"?>
<OFX>
    <BANKMSGSRSV1>
        <STMTTRNRS>
            <STMTRS>
                <BANKTRANLIST>
                    <STMTTRN>
                        <TRNTYPE>DEBIT</TRNTYPE>
                        <DTPOSTED>20250615</DTPOSTED>
                        <TRNAMT>-150.00</TRNAMT>
                        <NAME>Pagamento PIX</NAME>
                    </STMTTRN>
                    <STMTTRN>
                        <TRNTYPE>CREDIT</TRNTYPE>
                        <DTPOSTED>20250616</DTPOSTED>
                        <TRNAMT>500.00</TRNAMT>
                        <NAME>Transferência recebida</NAME>
                    </STMTTRN>
                </BANKTRANLIST>
            </STMTRS>
        </STMTTRNRS>
    </BANKMSGSRSV1>
</OFX>';
        
        $file = UploadedFile::fake()->createWithContent('extrato.ofx', $ofxContent);
        
        $result = $this->importService->importFile($file);
        
        $this->assertTrue($result['success']);
        $this->assertEquals('OFX', $result['format']);
        $this->assertGreaterThan(0, $result['count']);
    }
}
