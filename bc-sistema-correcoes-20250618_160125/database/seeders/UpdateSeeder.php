<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Update;

class UpdateSeeder extends Seeder
{
    public function run()
    {
        $updates = [
            [
                'version' => '1.1.0',
                'name' => 'Sistema de Importação de Extratos Aprimorado',
                'description' => 'Melhoria completa no sistema de importação com suporte a múltiplos formatos e detecção automática de bancos brasileiros.',
                'release_date' => now()->subDays(7),
                'download_url' => 'https://releases.bcsistema.com/updates/v1.1.0.zip',
                'file_size' => 2457600, // ~2.4MB
                'file_hash' => 'sha256:abc123...',
                'changes' => [
                    'Suporte completo a CSV, OFX, QIF, PDF e Excel',
                    'Detecção automática de bancos brasileiros (Itaú, Bradesco, Santander, BB, Caixa)',
                    'Interface melhorada para upload de arquivos',
                    'Validação avançada de formatos',
                    'Sistema de logs detalhado'
                ],
                'requirements' => [
                    'PHP >= 8.2',
                    'Laravel >= 11.0',
                    'Extensão ZIP habilitada',
                    'Mínimo 100MB espaço livre'
                ],
                'status' => 'available'
            ],
            [
                'version' => '1.2.0',
                'name' => 'Sistema de Atualizações Automáticas',
                'description' => 'Implementação do sistema de atualizações automáticas para facilitar deployments e manutenção.',
                'release_date' => now()->subDays(1),
                'download_url' => 'https://releases.bcsistema.com/updates/v1.2.0.zip',
                'file_size' => 1843200, // ~1.8MB
                'file_hash' => 'sha256:def456...',
                'changes' => [
                    'Sistema completo de atualizações automáticas',
                    'Interface web para gerenciar updates',
                    'Backup automático antes das atualizações',
                    'Verificação de integridade dos arquivos',
                    'Rollback automático em caso de falha',
                    'Logs detalhados de todo o processo'
                ],
                'requirements' => [
                    'PHP >= 8.2',
                    'Laravel >= 11.0',
                    'Extensão ZIP habilitada',
                    'Permissões de escrita no diretório raiz',
                    'Mínimo 200MB espaço livre'
                ],
                'status' => 'available'
            ],
            [
                'version' => '1.3.0',
                'name' => 'Dashboard Profissional e Relatórios Avançados',
                'description' => 'Nova versão do dashboard com gráficos interativos e sistema de relatórios profissional.',
                'release_date' => now()->addDays(3),
                'download_url' => 'https://releases.bcsistema.com/updates/v1.3.0.zip',
                'file_size' => 3276800, // ~3.2MB
                'file_hash' => 'sha256:ghi789...',
                'changes' => [
                    'Dashboard completamente redesenhado',
                    'Gráficos interativos com Chart.js',
                    'Relatórios em PDF/Excel profissionais',
                    'Filtros avançados por período',
                    'Widgets customizáveis',
                    'Tema escuro/claro',
                    'Performance otimizada'
                ],
                'requirements' => [
                    'PHP >= 8.2',
                    'Laravel >= 11.0',
                    'Node.js >= 18 (para assets)',
                    'Mínimo 150MB espaço livre'
                ],
                'status' => 'available'
            ]
        ];

        foreach ($updates as $updateData) {
            Update::create($updateData);
        }
    }
}
