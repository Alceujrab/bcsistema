# 🚀 INSTRUÇÕES RÁPIDAS DE APLICAÇÃO

## ❌ ERRO ANTERIOR
Você tentou executar um arquivo `.md` (documentação) como script bash.
Arquivos `.md` são documentos, não scripts executáveis.

## ✅ SOLUÇÃO CORRETA

### Opção 1: Script Automático
```bash
# 1. Faça download do arquivo de correção:
#    correcao-rotas-update-show-20250618_174532.tar.gz

# 2. Coloque os arquivos na raiz do projeto Laravel

# 3. Execute o script automático:
bash aplicar-correcoes.sh
```

### Opção 2: Manual
```bash
# 1. Extrair as correções
tar -xzf correcao-rotas-update-show-20250618_174532.tar.gz

# 2. Definir permissões
chmod -R 755 routes/
chmod -R 755 app/Http/Controllers/
chmod -R 755 resources/views/

# 3. Limpar cache
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

## 📦 Arquivos Necessários
- `correcao-rotas-update-show-20250618_174532.tar.gz` - Correções
- `aplicar-correcoes.sh` - Script automático (opcional)

## ✅ Resultado
Após aplicar as correções, teste:
https://usadosar.com.br/bc/system/update

## 🆘 Em Caso de Problemas
Se algo der errado, o script automático cria backup dos arquivos originais.
