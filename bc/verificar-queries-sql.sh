#!/bin/bash

echo "=== Verificando Queries SQL Problemáticas ==="

echo ""
echo "1. Verificando orderBy com aliases problemáticos:"
grep -r "orderBy('total'" app/Http/Controllers/ || echo "✓ Nenhum orderBy('total') encontrado"
grep -r "orderBy('count'" app/Http/Controllers/ || echo "✓ Nenhum orderBy('count') encontrado"  
grep -r "orderBy('sum'" app/Http/Controllers/ || echo "✓ Nenhum orderBy('sum') encontrado"
grep -r "orderBy('average'" app/Http/Controllers/ || echo "✓ Nenhum orderBy('average') encontrado"

echo ""
echo "2. Verificando se há orderByRaw corretos:"
grep -r "orderByRaw(" app/Http/Controllers/ && echo "✓ orderByRaw encontrados (corretos)"

echo ""
echo "3. Testando conexão com banco de dados:"
php artisan tinker --execute="echo 'Testando DB: '; try { DB::connection()->getPdo(); echo 'Conexão OK'; } catch(Exception \$e) { echo 'Erro: '.\$e->getMessage(); }"

echo ""
echo "4. Verificando estrutura das tabelas principais:"
php artisan tinker --execute="
try {
    echo 'Tabela transactions: ';
    \$columns = DB::select('DESCRIBE transactions');
    echo count(\$columns) . ' colunas encontradas\n';
} catch(Exception \$e) {
    echo 'Erro: ' . \$e->getMessage() . '\n';
}
"

echo ""
echo "=== Verificação Concluída ==="
