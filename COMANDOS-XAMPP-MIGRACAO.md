# SCRIPT DE MIGRAÇÃO PARA XAMPP - BC SISTEMA

## COMANDOS PARA EXECUTAR APÓS INSTALAR XAMPP

### 1. CRIAR BANCO DE DADOS
```sql
-- Executar no phpMyAdmin ou MySQL
CREATE DATABASE bc_sistema;
CREATE USER 'bc_user'@'localhost' IDENTIFIED BY 'bc_password';
GRANT ALL PRIVILEGES ON bc_sistema.* TO 'bc_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. CONFIGURAR .ENV PARA XAMPP
```env
APP_NAME="BC Sistema"
APP_ENV=local
APP_KEY=base64:CHAVE_SERA_GERADA
APP_DEBUG=true
APP_URL=http://localhost/bc

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bc_sistema
DB_USERNAME=bc_user
DB_PASSWORD=bc_password

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### 3. COMANDOS DE CONFIGURAÇÃO
```bash
# No diretório do projeto (C:\xampp\htdocs\bc)
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
php artisan optimize
```

### 4. ESTRUTURA DE DIRETÓRIOS XAMPP
```
C:\xampp\htdocs\
└── bc/                    ← Sistema BC aqui
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/           ← Ponto de entrada
    ├── resources/
    ├── routes/
    ├── storage/
    └── vendor/
```

### 5. PERMISSÕES (Windows)
- Pasta storage/ → Leitura/Escrita
- Pasta bootstrap/cache/ → Leitura/Escrita  
- Pasta public/ → Leitura

### 6. ACESSO FINAL
- URL: http://localhost/bc/public
- ou configurar Virtual Host para: http://bc-sistema.local

### 7. TESTE RÁPIDO
- Acessar dashboard
- Testar importação
- Verificar relatórios
- Validar transações

**PRONTO PARA EXECUÇÃO QUANDO XAMPP ESTIVER INSTALADO!**
