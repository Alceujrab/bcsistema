<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, json, boolean, integer, color
            $table->string('category')->default('general'); // general, appearance, dashboard, modules, advanced
            $table->string('label');
            $table->text('description')->nullable();
            $table->json('options')->nullable(); // Para select, checkbox, etc
            $table->boolean('is_public')->default(false); // Se pode ser acessado sem auth
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Inserir configurações padrão
        $settings = [
            // Aparência - Cores
            [
                'key' => 'primary_color',
                'value' => '#667eea',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor Primária',
                'description' => 'Cor principal do sistema, usada em botões e destaques',
                'options' => null,
                'is_public' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'secondary_color',
                'value' => '#764ba2',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor Secundária',
                'description' => 'Cor secundária para gradientes e elementos complementares',
                'options' => null,
                'is_public' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'success_color',
                'value' => '#28a745',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Sucesso',
                'description' => 'Cor para indicar sucesso e valores positivos',
                'options' => null,
                'is_public' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'danger_color',
                'value' => '#dc3545',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Alerta',
                'description' => 'Cor para alertas e valores negativos',
                'options' => null,
                'is_public' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'warning_color',
                'value' => '#ffc107',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Aviso',
                'description' => 'Cor para avisos e atenção',
                'options' => null,
                'is_public' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'info_color',
                'value' => '#17a2b8',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Informação',
                'description' => 'Cor para informações e dados neutros',
                'options' => null,
                'is_public' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Configurações Gerais
            [
                'key' => 'company_name',
                'value' => 'BC Sistema',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Nome da Empresa',
                'description' => 'Nome da empresa exibido no sistema',
                'options' => null,
                'is_public' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'company_logo',
                'value' => '',
                'type' => 'file',
                'category' => 'general',
                'label' => 'Logo da Empresa',
                'description' => 'Logo exibido no cabeçalho do sistema',
                'options' => null,
                'is_public' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'timezone',
                'value' => 'America/Sao_Paulo',
                'type' => 'select',
                'category' => 'general',
                'label' => 'Fuso Horário',
                'description' => 'Fuso horário padrão do sistema',
                'options' => json_encode([
                    'America/Sao_Paulo' => 'São Paulo (GMT-3)',
                    'America/New_York' => 'Nova York (GMT-5)',
                    'Europe/London' => 'Londres (GMT+0)',
                    'Europe/Berlin' => 'Berlim (GMT+1)'
                ]),
                'is_public' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'currency',
                'value' => 'BRL',
                'type' => 'select',
                'category' => 'general',
                'label' => 'Moeda Padrão',
                'description' => 'Moeda usada nos cálculos e exibições',
                'options' => json_encode([
                    'BRL' => 'Real Brasileiro (R$)',
                    'USD' => 'Dólar Americano ($)',
                    'EUR' => 'Euro (€)',
                    'GBP' => 'Libra Esterlina (£)'
                ]),
                'is_public' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Dashboard
            [
                'key' => 'dashboard_cards_per_row',
                'value' => '3',
                'type' => 'select',
                'category' => 'dashboard',
                'label' => 'Cards por Linha',
                'description' => 'Número de cards por linha no dashboard',
                'options' => json_encode([
                    '2' => '2 Cards',
                    '3' => '3 Cards',
                    '4' => '4 Cards',
                    '6' => '6 Cards'
                ]),
                'is_public' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'dashboard_refresh_interval',
                'value' => '300',
                'type' => 'integer',
                'category' => 'dashboard',
                'label' => 'Intervalo de Atualização (segundos)',
                'description' => 'Tempo para atualização automática dos dados',
                'options' => null,
                'is_public' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'show_welcome_banner',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'dashboard',
                'label' => 'Mostrar Banner de Boas-vindas',
                'description' => 'Exibir banner de boas-vindas no dashboard',
                'options' => null,
                'is_public' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Módulos
            [
                'key' => 'enable_clients_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Clientes',
                'description' => 'Habilitar módulo de gestão de clientes',
                'options' => null,
                'is_public' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_suppliers_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Fornecedores',
                'description' => 'Habilitar módulo de gestão de fornecedores',
                'options' => null,
                'is_public' => false,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'enable_reports_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Relatórios',
                'description' => 'Habilitar módulo de relatórios avançados',
                'options' => null,
                'is_public' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insert($setting);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
