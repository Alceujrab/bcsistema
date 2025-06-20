#!/bin/bash

# COMANDOS PARA LIMPAR TODOS OS CACHES DO LARAVEL
# Data: $(date +%Y-%m-%d)

echo "=== LIMPANDO TODOS OS CACHES DO LARAVEL ==="
echo

# 1. Cache de aplicação
echo "1. Limpando cache de aplicação..."
php artisan cache:clear
echo "   ✓ Cache de aplicação limpo"

# 2. Cache de configuração
echo "2. Limpando cache de configuração..."
php artisan config:clear
echo "   ✓ Cache de configuração limpo"

# 3. Cache de views
echo "3. Limpando cache de views..."
php artisan view:clear
echo "   ✓ Cache de views limpo"

# 4. Cache de rotas
echo "4. Limpando cache de rotas..."
php artisan route:clear
echo "   ✓ Cache de rotas limpo"

# 5. Cache de eventos
echo "5. Limpando cache de eventos..."
php artisan event:clear
echo "   ✓ Cache de eventos limpo"

# 6. Cache compilados
echo "6. Limpando arquivos compilados..."
php artisan clear-compiled
echo "   ✓ Arquivos compilados limpos"

# 7. Cache de otimização
echo "7. Limpando otimizações..."
php artisan optimize:clear
echo "   ✓ Otimizações limpas"

# 8. Cache de filas
echo "8. Limpando cache de filas..."
php artisan queue:clear 2>/dev/null || echo "   ⚠ Queue clear não disponível"

# 9. Cache de agendamentos
echo "9. Limpando cache de agendamentos..."
php artisan schedule:clear-cache 2>/dev/null || echo "   ⚠ Schedule clear não disponível"

# 10. Cache manual de arquivos
echo "10. Removendo arquivos de cache manualmente..."

# Cache de dados
if [ -d "storage/framework/cache/data" ]; then
    rm -rf storage/framework/cache/data/*
    echo "   ✓ Cache de dados removido"
fi

# Cache de views compiladas
if [ -d "storage/framework/views" ]; then
    rm -rf storage/framework/views/*
    echo "   ✓ Views compiladas removidas"
fi

# Cache de sessões (cuidado em produção!)
echo "11. Limpando sessões (opcional)..."
read -p "Deseja limpar as sessões? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if [ -d "storage/framework/sessions" ]; then
        rm -rf storage/framework/sessions/*
        echo "   ✓ Sessões removidas"
    fi
else
    echo "   ⚠ Sessões mantidas"
fi

# Cache do bootstrap
echo "12. Limpando cache do bootstrap..."
if [ -d "bootstrap/cache" ]; then
    rm -rf bootstrap/cache/*.php
    echo "   ✓ Cache do bootstrap limpo"
fi

# Cache do Composer
echo "13. Limpando cache do Composer..."
composer clear-cache 2>/dev/null || echo "   ⚠ Composer não encontrado"

# Cache do NPM
echo "14. Limpando cache do NPM..."
npm cache clean --force 2>/dev/null || echo "   ⚠ NPM não encontrado"

# Logs antigos (opcional)
echo "15. Limpando logs antigos..."
read -p "Deseja limpar os logs? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if [ -d "storage/logs" ]; then
        rm -rf storage/logs/*.log
        echo "   ✓ Logs removidos"
    fi
else
    echo "   ⚠ Logs mantidos"
fi

echo
echo "=== RECRIANDO CACHES NECESSÁRIOS ==="
echo

# Recriar caches importantes
echo "1. Recriando cache de configuração..."
php artisan config:cache
echo "   ✓ Cache de configuração recriado"

echo "2. Recriando cache de rotas..."
php artisan route:cache
echo "   ✓ Cache de rotas recriado"

echo "3. Otimizando aplicação..."
php artisan optimize 2>/dev/null || echo "   ⚠ Optimize não disponível"

echo
echo "=== VERIFICANDO PERMISSÕES ==="
echo

# Verificar e ajustar permissões
echo "Ajustando permissões dos diretórios de cache..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
echo "   ✓ Permissões ajustadas"

echo
echo "=== LIMPEZA COMPLETA CONCLUÍDA ==="
echo "Todos os caches foram limpos e os necessários foram recriados."
echo "Aplicação pronta para uso!"
echo

# Comandos individuais para referência
echo "=== COMANDOS INDIVIDUAIS PARA REFERÊNCIA ==="
echo "php artisan cache:clear          # Limpa cache de aplicação"
echo "php artisan config:clear         # Limpa cache de configuração"
echo "php artisan view:clear           # Limpa cache de views"
echo "php artisan route:clear          # Limpa cache de rotas"
echo "php artisan event:clear          # Limpa cache de eventos"
echo "php artisan clear-compiled       # Limpa arquivos compilados"
echo "php artisan optimize:clear       # Limpa todas as otimizações"
echo "php artisan queue:clear          # Limpa filas"
echo "php artisan schedule:clear-cache # Limpa cache de agendamentos"
echo
echo "RECRIAÇÃO:"
echo "php artisan config:cache         # Recria cache de configuração"
echo "php artisan route:cache          # Recria cache de rotas"
echo "php artisan optimize             # Otimiza aplicação"
echo
