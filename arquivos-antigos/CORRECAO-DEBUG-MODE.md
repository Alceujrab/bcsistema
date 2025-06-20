# CORREÇÃO: Chave "debug_mode" Não Definida

## ❌ Problema
```
Undefined array key "debug_mode"
resources/views/system/update/index.blade.php:258
```

## ✅ Solução Aplicada

### Arquivo Corrigido
**`bc/app/Http/Controllers/UpdateController.php`**

### Chaves Adicionadas no Método `getSystemInfo()`
```php
'debug_mode' => config('app.debug', false),
'database_driver' => config('database.default', 'mysql'),
```

## 📦 Arquivo para Upload
```
correcao-rotas-update-show-20250618_172737.tar.gz
```

## 🚀 Instruções de Aplicação

### 1. Download do Arquivo
Baixe: `correcao-rotas-update-show-20250618_172737.tar.gz`

### 2. Upload no Servidor
Faça upload para a raiz do projeto Laravel

### 3. Extrair
```bash
tar -xzf correcao-rotas-update-show-20250618_172737.tar.gz
```

### 4. Limpar Cache
```bash
php artisan config:clear
php artisan view:clear
```

## ✅ Resultado
Após aplicar a correção:
- ✅ Chave `debug_mode` definida corretamente
- ✅ Chave `database_driver` definida corretamente  
- ✅ Página https://usadosar.com.br/bc/system/update funcionando sem erros

## 📋 Arquivo Alterado
- `bc/app/Http/Controllers/UpdateController.php`
