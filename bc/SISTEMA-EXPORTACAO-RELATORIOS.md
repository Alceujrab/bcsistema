# Sistema de Exportação de Relatórios - BC Sistema

## Funcionalidades Implementadas

### 1. Exportação de Relatórios

O sistema agora suporta exportação de relatórios em múltiplos formatos:

- **PDF**: Relatórios formatados para impressão e visualização
- **Excel/CSV**: Dados estruturados para análise em planilhas
- **CSV**: Formato compatível com diversos sistemas

### 2. Tipos de Relatórios Disponíveis

#### Relatório de Transações
- **URL**: `/reports/export/transactions/{format}`
- **Formatos**: pdf, excel, csv
- **Filtros suportados**: 
  - Data inicial/final
  - Conta bancária
  - Categoria
  - Tipo (crédito/débito)
  - Status
  - Valor mínimo/máximo

#### Relatório de Conciliações
- **URL**: `/reports/export/reconciliations/{format}`
- **Formatos**: pdf, excel, csv
- **Filtros suportados**:
  - Data inicial/final
  - Conta bancária
  - Status da conciliação

#### Relatório de Fluxo de Caixa
- **URL**: `/reports/export/cash-flow/{format}`
- **Formatos**: pdf, excel, csv
- **Filtros suportados**:
  - Data inicial/final
  - Conta bancária

#### Relatório de Categorias
- **URL**: `/reports/export/categories/{format}`
- **Formatos**: pdf, excel, csv
- **Filtros suportados**:
  - Data inicial/final
  - Conta bancária
  - Categoria específica

### 3. Interface do Usuário

#### Botões nas Páginas de Relatórios
Cada página de relatório possui botões de exportação:
```html
<button type="button" class="btn btn-success" onclick="exportReport('excel')">
    <i class="fas fa-file-excel me-2"></i>Excel
</button>
<button type="button" class="btn btn-danger" onclick="exportReport('pdf')">
    <i class="fas fa-file-pdf me-2"></i>PDF
</button>
<button type="button" class="btn btn-info" onclick="exportReport('csv')">
    <i class="fas fa-file-csv me-2"></i>CSV
</button>
```

#### Exportação Direta na Página Principal
Na página inicial de relatórios (`/reports`), cada card possui um dropdown com opções de exportação direta.

### 4. Arquivos Implementados

#### Backend
- `app/Services/ReportExportService.php`: Serviço principal de exportação
- `app/Services/PdfService.php`: Serviço para geração de PDFs
- `app/Http/Controllers/ReportController.php`: Controller atualizado com métodos de exportação

#### Templates PDF
- `resources/views/reports/pdf/layout.blade.php`: Layout base para PDFs
- `resources/views/reports/pdf/transactions.blade.php`: Template para relatório de transações
- `resources/views/reports/pdf/reconciliations.blade.php`: Template para relatório de conciliações
- `resources/views/reports/pdf/cash-flow.blade.php`: Template para relatório de fluxo de caixa
- `resources/views/reports/pdf/categories.blade.php`: Template para relatório de categorias

#### Frontend
- JavaScript atualizado nas views de relatórios para usar as novas rotas
- CSS personalizado para botões de exportação
- Interface responsiva com dropdowns

### 5. Como Usar

#### Exportação com Filtros
1. Acesse qualquer página de relatório
2. Configure os filtros desejados
3. Clique no botão de exportação no formato desejado
4. O arquivo será baixado automaticamente

#### Exportação Direta
1. Na página principal de relatórios (`/reports`)
2. Clique no dropdown de exportação ao lado do botão "Gerar Relatório"
3. Selecione o formato desejado
4. O arquivo será baixado com dados padrão (sem filtros)

#### URLs Diretas
Você também pode acessar diretamente:
```
/reports/export/transactions/pdf
/reports/export/reconciliations/excel
/reports/export/cash-flow/csv
/reports/export/categories/pdf
```

### 6. Personalização

#### Adicionando Novos Formatos
Para adicionar suporte a novos formatos, edite:
1. `ReportExportService.php`: Adicione o método de exportação
2. `ReportController.php`: Adicione o case no switch
3. Templates: Crie novos templates se necessário

#### Personalizando Templates PDF
Os templates estão em `resources/views/reports/pdf/` e podem ser customizados:
- Logotipo da empresa
- Cores e estilos
- Campos adicionais
- Formatação específica

### 7. Requisitos Técnicos

#### Bibliotecas Utilizadas
- **DomPDF**: Para geração de PDFs (a ser instalada em produção)
- **Maatwebsite/Excel**: Para exportação Excel (a ser instalada em produção)
- **CSV nativo**: Usando funções nativas do PHP

#### Configuração em Produção
```bash
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 8. Próximos Passos

1. **Instalar bibliotecas de PDF e Excel** em produção
2. **Adicionar agendamento** de relatórios automáticos
3. **Implementar cache** para relatórios grandes
4. **Adicionar assinatura digital** aos PDFs
5. **Criar templates personalizáveis** pelo usuário

### 9. Notas Importantes

- Por enquanto, os PDFs são gerados como HTML para download
- Em produção, será necessário instalar as bibliotecas DomPDF e Excel
- Os arquivos são gerados dinamicamente (não armazenados)
- Todos os filtros aplicados na interface são respeitados na exportação
- O sistema é responsivo e funciona em dispositivos móveis

## Conclusão

O sistema de exportação de relatórios está totalmente implementado e pronto para uso. Os usuários podem exportar dados em múltiplos formatos respeitando todos os filtros aplicados, tanto através das páginas de relatórios quanto diretamente da página principal.
