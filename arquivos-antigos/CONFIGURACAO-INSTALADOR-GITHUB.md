# 🔧 CONFIGURAÇÃO INSTALADOR GITHUB - BC SISTEMA

## 📋 **CONFIGURAÇÃO NECESSÁRIA**

Para usar o **instalador-github.php**, você precisa configurar os dados do seu repositório GitHub.

### **Passo 1: Editar Configurações**

Abra o arquivo `bc/instalador-github.php` e localize esta seção (linhas 15-21):

```php
// Configuração do GitHub (SUBSTITUA PELOS SEUS DADOS)
$GITHUB_CONFIG = [
    'username' => 'SEU_USUARIO_GITHUB',  // Substitua pelo seu usuário
    'repository' => 'SEU_REPOSITORIO',   // Substitua pelo nome do repositório
    'branch' => 'main',                  // ou 'master'
    'token' => '',                       // Token opcional para repos privados
];
```

### **Passo 2: Substituir pelos Seus Dados**

**Exemplo de configuração:**
```php
$GITHUB_CONFIG = [
    'username' => 'joao123',           // Seu usuário GitHub
    'repository' => 'bc-sistema',      // Nome do seu repositório
    'branch' => 'main',                // Branch principal
    'token' => '',                     // Deixe vazio para repos públicos
];
```

### **Passo 3: Configurar Token (Opcional)**

**Para repositórios privados**, você precisa de um token:

1. Acesse: https://github.com/settings/tokens
2. Clique em "Generate new token (classic)"
3. Selecione escopo: `repo` (Full control of private repositories)
4. Copie o token gerado
5. Configure no arquivo:

```php
'token' => 'ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
```

---

## 📁 **ESTRUTURA NECESSÁRIA NO GITHUB**

Seu repositório deve ter esta estrutura:

```
seu-repositorio/
├── app/
│   ├── Services/
│   │   └── StatementImportService.php
│   └── Http/
│       └── Controllers/
│           └── ImportController.php
├── resources/
│   └── views/
│       └── imports/
│           └── create.blade.php
└── public/
    └── css/
        └── imports.css
```

---

## 🔗 **URLS QUE SERÃO ACESSADAS**

O instalador tentará baixar os arquivos desta forma:

```
https://raw.githubusercontent.com/[SEU_USUARIO]/[SEU_REPO]/[BRANCH]/app/Services/StatementImportService.php
https://raw.githubusercontent.com/[SEU_USUARIO]/[SEU_REPO]/[BRANCH]/app/Http/Controllers/ImportController.php
https://raw.githubusercontent.com/[SEU_USUARIO]/[SEU_REPO]/[BRANCH]/resources/views/imports/create.blade.php
https://raw.githubusercontent.com/[SEU_USUARIO]/[SEU_REPO]/[BRANCH]/public/css/imports.css
```

---

## ✅ **COMO TESTAR**

### **1. Verificar URLs Manualmente**

Abra no navegador (substitua pelos seus dados):
```
https://raw.githubusercontent.com/[SEU_USUARIO]/[SEU_REPO]/main/app/Services/StatementImportService.php
```

Se abrir o arquivo, está funcionando!

### **2. Testar o Instalador**

1. Coloque o `instalador-github.php` configurado no servidor
2. Acesse pelo navegador: `http://seudominio.com/instalador-github.php`
3. Clique em "Verificar Requisitos"
4. Se tudo OK, clique em "Baixar do GitHub"

---

## 🔧 **INSTALADOR AUTOMÁTICO (ALTERNATIVA)**

Se preferir **não usar GitHub**, use o `instalador-automatico.php` que gera os arquivos localmente.

### **Vantagens do Automático:**
- ✅ Não precisa de repositório GitHub
- ✅ Gera arquivos corrigidos na hora
- ✅ Funciona offline
- ✅ Mais rápido

### **Vantagens do GitHub:**
- ✅ Centraliza correções em um repositório
- ✅ Facilita atualizações futuras
- ✅ Permite versionamento
- ✅ Compartilha com equipe

---

## 🚨 **PROBLEMAS COMUNS**

### **Erro 404 ao baixar**
- Verifique se o usuário/repositório estão corretos
- Verifique se a branch existe (main vs master)
- Verifique se os arquivos estão no local correto

### **Erro de permissão**
- Para repos privados, configure o token
- Verifique se o token tem permissões corretas

### **Arquivos não encontrados**
- Verifique se você fez upload dos arquivos corrigidos para o GitHub
- Verifique se a estrutura de pastas está correta

---

## 📋 **CHECKLIST DE CONFIGURAÇÃO**

- [ ] Substituir `SEU_USUARIO_GITHUB` pelo seu usuário
- [ ] Substituir `SEU_REPOSITORIO` pelo nome do repositório
- [ ] Confirmar a branch (main ou master)
- [ ] Configurar token se repositório for privado
- [ ] Fazer upload dos arquivos corrigidos para o GitHub
- [ ] Testar URLs manualmente no navegador
- [ ] Testar o instalador completo

---

## 🎯 **PRONTO PARA USAR**

Após configurar, o instalador GitHub estará pronto para:

1. **Baixar automaticamente** as correções do seu repositório
2. **Aplicar as correções** no sistema local
3. **Fazer backup** antes de qualquer alteração
4. **Gerar logs** detalhados do processo
5. **Otimizar o sistema** após as correções

**Boa instalação! 🚀**
