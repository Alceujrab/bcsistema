# 🚀 INSTALADOR BC SISTEMA - GUIA RÁPIDO

## ⚡ INSTALAÇÃO EM 3 PASSOS

### 1. 📁 Fazer Upload dos Arquivos
```
install.php          → Raiz do projeto Laravel
test-installer.sh    → Raiz do projeto Laravel (opcional)
```

### 2. 🧪 Testar Pré-requisitos (Opcional)
```bash
chmod +x test-installer.sh
./test-installer.sh
```

### 3. 🚀 Executar Instalação

#### Via Navegador (Recomendado):
```
http://seudominio.com/install.php
```

#### Via Terminal:
```bash
php install.php
```

---

## 🎯 O QUE SERÁ CORRIGIDO

✅ **Importação PDF** - Suporte a todos os bancos brasileiros  
✅ **Conversão Excel** - Processamento automático .xls/.xlsx  
✅ **Interface CSS** - Dropzone responsivo com animações  
✅ **Validações** - Suporte a 7 formatos, até 20MB  
✅ **Otimização** - Cache de rotas e configurações  

---

## 🏦 BANCOS SUPORTADOS

- Banco do Brasil
- Itaú
- Bradesco  
- Santander
- Caixa Econômica
- Padrão Genérico

---

## 🔒 SEGURANÇA

- ✅ Backup automático em `storage/backups/`
- ✅ Validação de sintaxe PHP
- ✅ Verificação de dependências
- ✅ Log detalhado em `install.log`

---

## 📞 SUPORTE

### ✅ Após Instalação Bem-sucedida
O sistema estará pronto para importar extratos PDF e Excel imediatamente.

### ❌ Em Caso de Erro
1. Verifique `install.log`
2. Consulte backups em `storage/backups/`
3. Execute `php artisan config:clear`

---

**⏱️ Tempo estimado: 2-5 minutos**  
**🚀 Sistema BC v2.0 - Modo Profissional**
