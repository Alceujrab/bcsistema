# 🚀 INSTALADOR AUTOMÁTICO - SISTEMA BC v2.0

## Correções Profissionais para Importação de Extratos

Este instalador aplica automaticamente todas as correções necessárias para resolver problemas de importação de PDF, Excel e melhorias de interface no Sistema BC.

---

## 📋 O QUE SERÁ INSTALADO

### ✅ Correções Críticas
- **Parser PDF Avançado**: Suporte a 7 padrões de bancos brasileiros
- **Conversão Excel Automática**: Processamento direto de arquivos .xls/.xlsx
- **Interface Melhorada**: CSS organizado com animações profissionais
- **Validações Robustas**: Suporte ampliado a múltiplos formatos

### 🏦 Bancos Suportados
- Banco do Brasil (DD/MM/YYYY)
- Itaú (DD/MM + D/C)
- Bradesco (DD/MM/YY)
- Santander (DD-MM-YYYY)
- Caixa Econômica (DD/MM/YYYY + R$)
- Padrão Genérico Brasileiro

### 📁 Formatos Suportados
- **PDF**: Extratos bancários com OCR básico
- **Excel**: .xls e .xlsx com conversão automática
- **CSV**: Processamento otimizado
- **TXT**: Arquivos de texto estruturados
- **OFX**: Open Financial Exchange
- **QIF**: Quicken Interchange Format

---

## 🔧 COMO USAR

### Método 1: Via Navegador (Recomendado)
1. Faça upload do arquivo `install.php` para a raiz do seu projeto Laravel
2. Acesse via navegador: `http://seudominio.com/install.php`
3. Aguarde a instalação automática
4. Visualize o relatório completo na tela

### Método 2: Via Linha de Comando
```bash
# Navegue até o diretório do projeto
cd /caminho/para/seu/projeto

# Execute o instalador
php install.php

# Verifique o log de instalação
cat install.log
```

---

## 🔍 REQUISITOS DO SISTEMA

### Obrigatórios
- ✅ PHP 8.2 ou superior
- ✅ Laravel 11.x
- ✅ Composer instalado
- ✅ Permissões de escrita nas pastas do projeto

### Dependências (Verificadas Automaticamente)
- ✅ `barryvdh/laravel-dompdf`
- ✅ `maatwebsite/excel`
- ✅ `smalot/pdfparser`
- ✅ `league/csv`

---

## 🛡️ SEGURANÇA E BACKUP

### Backup Automático
O instalador cria backups automáticos de todos os arquivos modificados:
- **Localização**: `storage/backups/install_YYYY-MM-DD_HH-MM-SS/`
- **Arquivos**: Controllers, Services, Views modificadas

### Rollback Manual
Para reverter as alterações (se necessário):
```bash
# Restaurar arquivos do backup
cp storage/backups/install_*/StatementImportService.php app/Services/
cp storage/backups/install_*/ImportController.php app/Http/Controllers/
cp storage/backups/install_*/create.blade.php resources/views/imports/

# Limpar cache
php artisan config:clear
php artisan route:clear
```

---

## 📊 FUNCIONALIDADES INSTALADAS

### 1. 🔍 Parser PDF Inteligente
```php
// Padrões implementados
$patterns = [
    '/(\d{2}\/\d{2}\/\d{4})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})/', // BB
    '/(\d{2}\/\d{2})\s+(.+?)\s+([\-\+]?\d{1,3}(?:\.\d{3})*[,]\d{2})\s*[DC]?/', // Itaú
    // + 5 padrões adicionais
];
```

### 2. 📊 Conversão Excel Automática
- Processamento direto de planilhas
- Conversão automática para CSV
- Validação de estrutura de dados
- Tratamento de caracteres especiais

### 3. 🎨 Interface Profissional
- CSS organizado (200+ linhas)
- Drag & drop responsivo
- Animações suaves
- Feedback visual em tempo real

### 4. 🔧 Validações Avançadas
```php
'file' => 'required|file|max:20480|mimes:csv,txt,ofx,qif,pdf,xls,xlsx'
```

---

## 🧪 TESTES AUTOMÁTICOS

O instalador executa testes automáticos para garantir que tudo funciona:

### Verificações Realizadas
- ✅ Sintaxe PHP válida
- ✅ Dependências instaladas
- ✅ Permissões de arquivo
- ✅ Estrutura de diretórios
- ✅ Rotas funcionais

### Log Detalhado
Todos os passos são registrados em `install.log`:
```
[2025-06-20 14:30:15] 🚀 INICIANDO INSTALAÇÃO - SISTEMA BC v2.0
[2025-06-20 14:30:16] 🔍 Verificando requisitos do sistema...
[2025-06-20 14:30:17] ✅ Todos os requisitos atendidos
[2025-06-20 14:30:18] 💾 Criando backups de segurança...
[2025-06-20 14:30:19] 🔧 Atualizando StatementImportService...
[2025-06-20 14:30:20] ✅ StatementImportService atualizado com sucesso
```

---

## 🚨 RESOLUÇÃO DE PROBLEMAS

### Erro: "PHP 8.2+ é necessário"
```bash
# Verificar versão do PHP
php -v

# Atualizar PHP (Ubuntu/Debian)
sudo apt update
sudo apt install php8.2
```

### Erro: "Laravel não encontrado"
```bash
# Verificar se está no diretório correto
ls -la artisan

# Se não existir, você não está na raiz do projeto Laravel
```

### Erro: "Dependências do Composer não instaladas"
```bash
# Instalar dependências
composer install --no-dev --optimize-autoloader
```

### Erro: "Sem permissão de escrita"
```bash
# Ajustar permissões (Linux/Mac)
chmod -R 755 app/
chmod -R 755 public/
chmod -R 755 resources/
chmod -R 755 storage/
```

---

## 📈 APÓS A INSTALAÇÃO

### Testes Recomendados
1. **Importar PDF**: Teste com extrato do seu banco
2. **Importar Excel**: Teste com planilha .xlsx
3. **Interface**: Verificar drag & drop funcionando
4. **Performance**: Monitorar tempo de processamento

### Monitoramento
- **Logs Laravel**: `storage/logs/laravel.log`
- **Log Instalação**: `install.log`
- **Backup**: `storage/backups/`

### Limpeza (Opcional)
```bash
# Remover instalador após uso
rm install.php
rm install.log

# Manter apenas os backups
```

---

## 🎯 PRÓXIMOS PASSOS

### Configurações Opcionais
1. **OCR Avançado**: Instalar Tesseract para PDFs de imagem
2. **Queue Processing**: Para arquivos muito grandes
3. **Monitoring**: Dashboard de importações

### Otimizações Futuras
1. **Cache Redis**: Para melhor performance
2. **CDN**: Para assets estáticos
3. **API**: Importação via API REST

---

## 📞 SUPORTE

### Em Caso de Problemas
1. Verifique o arquivo `install.log`
2. Consulte os backups em `storage/backups/`
3. Execute `php artisan config:clear`
4. Teste manualmente cada funcionalidade

### Recursos Úteis
- **Documentação Laravel**: https://laravel.com/docs
- **Laravel Excel**: https://laravel-excel.com
- **DomPDF**: https://github.com/barryvdh/laravel-dompdf

---

## ✅ CHECKLIST PÓS-INSTALAÇÃO

- [ ] Instalação executada sem erros
- [ ] Backup criado automaticamente
- [ ] Teste de importação PDF realizado
- [ ] Teste de importação Excel realizado
- [ ] Interface funcionando corretamente
- [ ] Performance satisfatória
- [ ] Log de erros limpo

---

**🚀 Sistema BC v2.0 - Pronto para uso profissional!**

*Instalador criado em 20/06/2025 - Modo Profissional Ativado*
