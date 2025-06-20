<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\BankAccount;
use App\Models\User;

class ExtractImportIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar usuário e autenticar
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
        
        // Criar conta bancária
        $this->bankAccount = BankAccount::factory()->create([
            'name' => 'Conta Corrente Teste',
            'bank' => 'Itaú',
            'account_number' => '12345-6',
            'balance' => 1000.00,
            'active' => true
        ]);
        
        Storage::fake('local');
    }

    public function test_user_can_access_import_page()
    {
        $response = $this->get(route('extract-imports.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('imports.index');
        $response->assertSee('Importação de Extratos');
        $response->assertSee('Multi-Formato');
    }

    public function test_user_can_access_create_import_page()
    {
        $response = $this->get(route('extract-imports.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('imports.create');
        $response->assertSee('Nova Importação');
        $response->assertSee($this->bankAccount->name);
    }

    public function test_user_can_upload_csv_extract()
    {
        // Criar arquivo CSV válido
        $csvContent = "Data,Descrição,Valor,Saldo\n";
        $csvContent .= "18/06/2025,PIX RECEBIDO,500.00,1500.00\n";
        $csvContent .= "17/06/2025,COMPRA CARTAO,-150.00,1000.00\n";
        $csvContent .= "16/06/2025,TED ENVIADO,-200.00,1200.00";
        
        $file = UploadedFile::fake()->createWithContent('extrato_teste.csv', $csvContent);
        
        $response = $this->post(route('extract-imports.store'), [
            'file' => $file,
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement',
            'encoding' => 'UTF-8',
            'delimiter' => ',',
            'date_format' => 'd/m/Y',
            'skip_duplicates' => true
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verificar se as transações foram criadas
        $this->assertDatabaseCount('transactions', 3);
        
        // Verificar se o log de importação foi criado
        $this->assertDatabaseHas('import_logs', [
            'filename' => 'extrato_teste.csv',
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement',
            'total_records' => 3
        ]);
    }

    public function test_user_can_upload_ofx_extract()
    {
        $ofxContent = '<?xml version="1.0" encoding="UTF-8"?>
<OFX>
    <BANKMSGSRSV1>
        <STMTTRNRS>
            <STMTRS>
                <BANKTRANLIST>
                    <STMTTRN>
                        <TRNTYPE>DEBIT</TRNTYPE>
                        <DTPOSTED>20250618</DTPOSTED>
                        <TRNAMT>-100.50</TRNAMT>
                        <NAME>COMPRA CARTÃO</NAME>
                        <MEMO>Supermercado ABC</MEMO>
                    </STMTTRN>
                    <STMTTRN>
                        <TRNTYPE>CREDIT</TRNTYPE>
                        <DTPOSTED>20250617</DTPOSTED>
                        <TRNAMT>250.00</TRNAMT>
                        <NAME>PIX RECEBIDO</NAME>
                        <MEMO>João Silva</MEMO>
                    </STMTTRN>
                </BANKTRANLIST>
            </STMTRS>
        </STMTTRNRS>
    </BANKMSGSRSV1>
</OFX>';
        
        $file = UploadedFile::fake()->createWithContent('extrato.ofx', $ofxContent);
        
        $response = $this->post(route('extract-imports.store'), [
            'file' => $file,
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement',
            'skip_duplicates' => true
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        // Verificar transações OFX
        $this->assertDatabaseCount('transactions', 2);
        $this->assertDatabaseHas('transactions', [
            'description' => 'COMPRA CARTÃO',
            'amount' => -100.50
        ]);
        $this->assertDatabaseHas('transactions', [
            'description' => 'PIX RECEBIDO',
            'amount' => 250.00
        ]);
    }

    public function test_validation_fails_for_invalid_file()
    {
        $file = UploadedFile::fake()->create('documento.txt', 100);
        
        $response = $this->post(route('extract-imports.store'), [
            'file' => $file,
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement'
        ]);
        
        $response->assertSessionHasErrors(['file']);
    }

    public function test_validation_fails_for_missing_bank_account()
    {
        $csvContent = "Data,Descrição,Valor\n18/06/2025,Teste,100.00";
        $file = UploadedFile::fake()->createWithContent('extrato.csv', $csvContent);
        
        $response = $this->post(route('extract-imports.store'), [
            'file' => $file,
            'import_type' => 'bank_statement'
            // bank_account_id ausente
        ]);
        
        $response->assertSessionHasErrors(['bank_account_id']);
    }

    public function test_user_can_download_csv_template()
    {
        $response = $this->get(route('extract-imports.template', 'csv'));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="template_csv_extrato.csv"');
    }

    public function test_user_can_view_import_history()
    {
        // Criar alguns registros de importação
        \DB::table('import_logs')->insert([
            [
                'filename' => 'extrato1.csv',
                'bank_account_id' => $this->bankAccount->id,
                'import_type' => 'bank_statement',
                'total_records' => 10,
                'imported_records' => 8,
                'skipped_records' => 2,
                'format' => 'CSV',
                'status' => 'completed',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1)
            ],
            [
                'filename' => 'extrato2.ofx',
                'bank_account_id' => $this->bankAccount->id,
                'import_type' => 'credit_card',
                'total_records' => 5,
                'imported_records' => 5,
                'skipped_records' => 0,
                'format' => 'OFX',
                'status' => 'completed',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2)
            ]
        ]);
        
        $response = $this->get(route('extract-imports.history'));
        
        $response->assertStatus(200);
        $response->assertSee('extrato1.csv');
        $response->assertSee('extrato2.ofx');
        $response->assertSee('CSV');
        $response->assertSee('OFX');
    }

    public function test_duplicate_transactions_are_skipped()
    {
        // Primeira importação
        $csvContent = "Data,Descrição,Valor,Saldo\n";
        $csvContent .= "18/06/2025,PIX RECEBIDO,500.00,1500.00\n";
        $csvContent .= "17/06/2025,COMPRA CARTAO,-150.00,1000.00";
        
        $file1 = UploadedFile::fake()->createWithContent('extrato1.csv', $csvContent);
        
        $this->post(route('extract-imports.store'), [
            'file' => $file1,
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement',
            'skip_duplicates' => true
        ]);
        
        $this->assertDatabaseCount('transactions', 2);
        
        // Segunda importação com transações duplicadas
        $file2 = UploadedFile::fake()->createWithContent('extrato2.csv', $csvContent);
        
        $response = $this->post(route('extract-imports.store'), [
            'file' => $file2,
            'bank_account_id' => $this->bankAccount->id,
            'import_type' => 'bank_statement',
            'skip_duplicates' => true
        ]);
        
        // Deveria continuar com apenas 2 transações (duplicadas ignoradas)
        $this->assertDatabaseCount('transactions', 2);
        
        // Verificar que a mensagem informa sobre duplicatas
        $response->assertSessionHas('success');
    }
}
