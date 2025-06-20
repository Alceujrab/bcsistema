<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Executar seeder de configurações do sistema
     */
    public function run(): void
    {
        $settings = [
            // Aparência - Cores
            [
                'key' => 'primary_color',
                'value' => '#667eea',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor Primária',
                'description' => 'Cor principal do sistema, usada em botões e destaques',
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'secondary_color',
                'value' => '#764ba2',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor Secundária',
                'description' => 'Cor secundária para gradientes e elementos complementares',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'success_color',
                'value' => '#28a745',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Sucesso',
                'description' => 'Cor para indicar sucesso e valores positivos',
                'is_public' => true,
                'sort_order' => 3
            ],
            [
                'key' => 'danger_color',
                'value' => '#dc3545',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Alerta',
                'description' => 'Cor para alertas e valores negativos',
                'is_public' => true,
                'sort_order' => 4
            ],
            [
                'key' => 'warning_color',
                'value' => '#ffc107',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Aviso',
                'description' => 'Cor para avisos e atenção',
                'is_public' => true,
                'sort_order' => 5
            ],
            [
                'key' => 'info_color',
                'value' => '#17a2b8',
                'type' => 'color',
                'category' => 'appearance',
                'label' => 'Cor de Informação',
                'description' => 'Cor para informações e dados neutros',
                'is_public' => true,
                'sort_order' => 6
            ],

            // Configurações Gerais
            [
                'key' => 'company_name',
                'value' => 'BC Sistema',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Nome da Empresa',
                'description' => 'Nome da empresa exibido no sistema',
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'company_logo',
                'value' => '',
                'type' => 'file',
                'category' => 'general',
                'label' => 'Logo da Empresa',
                'description' => 'Logo exibido no cabeçalho do sistema',
                'is_public' => true,
                'sort_order' => 2
            ],
            [
                'key' => 'timezone',
                'value' => 'America/Sao_Paulo',
                'type' => 'select',
                'category' => 'general',
                'label' => 'Fuso Horário',
                'description' => 'Fuso horário padrão do sistema',
                'options' => [
                    'America/Sao_Paulo' => 'São Paulo (GMT-3)',
                    'America/New_York' => 'Nova York (GMT-5)',
                    'Europe/London' => 'Londres (GMT+0)',
                    'Europe/Berlin' => 'Berlim (GMT+1)'
                ],
                'is_public' => false,
                'sort_order' => 3
            ],
            [
                'key' => 'currency',
                'value' => 'BRL',
                'type' => 'select',
                'category' => 'general',
                'label' => 'Moeda Padrão',
                'description' => 'Moeda usada nos cálculos e exibições',
                'options' => [
                    'BRL' => 'Real Brasileiro (R$)',
                    'USD' => 'Dólar Americano ($)',
                    'EUR' => 'Euro (€)',
                    'GBP' => 'Libra Esterlina (£)'
                ],
                'is_public' => true,
                'sort_order' => 4
            ],

            // Dashboard
            [
                'key' => 'dashboard_cards_per_row',
                'value' => '3',
                'type' => 'select',
                'category' => 'dashboard',
                'label' => 'Cards por Linha',
                'description' => 'Número de cards por linha no dashboard',
                'options' => [
                    '2' => '2 Cards',
                    '3' => '3 Cards',
                    '4' => '4 Cards',
                    '6' => '6 Cards'
                ],
                'is_public' => true,
                'sort_order' => 1
            ],
            [
                'key' => 'dashboard_refresh_interval',
                'value' => '300',
                'type' => 'integer',
                'category' => 'dashboard',
                'label' => 'Intervalo de Atualização (segundos)',
                'description' => 'Tempo para atualização automática dos dados',
                'is_public' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'show_welcome_banner',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'dashboard',
                'label' => 'Mostrar Banner de Boas-vindas',
                'description' => 'Exibir banner de boas-vindas no dashboard',
                'is_public' => true,
                'sort_order' => 3
            ],

            // Módulos
            [
                'key' => 'enable_clients_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Clientes',
                'description' => 'Habilitar módulo de gestão de clientes',
                'is_public' => false,
                'sort_order' => 1
            ],
            [
                'key' => 'enable_suppliers_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Fornecedores',
                'description' => 'Habilitar módulo de gestão de fornecedores',
                'is_public' => false,
                'sort_order' => 2
            ],
            [
                'key' => 'enable_reports_module',
                'value' => 'true',
                'type' => 'boolean',
                'category' => 'modules',
                'label' => 'Módulo de Relatórios',
                'description' => 'Habilitar módulo de relatórios avançados',
                'is_public' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Configurações do sistema criadas com sucesso!');
    }
}
