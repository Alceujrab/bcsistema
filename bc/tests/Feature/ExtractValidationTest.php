<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

class ExtractValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_validate_csv_file_structure()
    {
        // CSV com estrutura válida
        $validCsv = "Data,Descrição,Valor,Saldo\n18/06/2025,Teste,100.00,1100.00";
        $file = UploadedFile::fake()->createWithContent('valid.csv', $validCsv);
        
        $response = $this->postJson(route('extract-imports.validate'), [
            'file' => $file,
            'format' => 'csv'
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'valid',
            'format',
            'detected_bank',
            'encoding',
            'delimiter',
            'headers',
            'sample_data',
            'recommendations'
        ]);
    }

    public function test_can_validate_ofx_file_structure()
    {
        $validOfx = '<?xml version="1.0" encoding="UTF-8"?>
<OFX>
    <BANKMSGSRSV1>
        <STMTTRNRS>
            <STMTRS>
                <BANKTRANLIST>
                    <STMTTRN>
                        <TRNTYPE>CREDIT</TRNTYPE>
                        <DTPOSTED>20250618</DTPOSTED>
                        <TRNAMT>100.00</TRNAMT>
                        <NAME>Teste</NAME>
                    </STMTTRN>
                </BANKTRANLIST>
            </STMTRS>
        </STMTTRNRS>
    </BANKMSGSRSV1>
</OFX>';
        
        $file = UploadedFile::fake()->createWithContent('valid.ofx', $validOfx);
        
        $response = $this->postJson(route('extract-imports.validate'), [
            'file' => $file,
            'format' => 'ofx'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'valid' => true,
            'format' => 'OFX'
        ]);
    }

    public function test_detects_invalid_file_format()
    {
        $invalidFile = UploadedFile::fake()->create('document.doc', 100);
        
        $response = $this->postJson(route('extract-imports.validate'), [
            'file' => $invalidFile,
            'format' => 'csv'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'valid',
            'errors'
        ]);
    }

    public function test_detects_csv_encoding_issues()
    {
        // Simular arquivo com encoding incorreto
        $csvWithAccents = "Data,Descrição,Valor\n18/06/2025,Transferência,100.00";
        $file = UploadedFile::fake()->createWithContent('accents.csv', $csvWithAccents);
        
        $response = $this->postJson(route('extract-imports.validate'), [
            'file' => $file,
            'format' => 'csv'
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'encoding' => 'UTF-8'
        ]);
    }
}
