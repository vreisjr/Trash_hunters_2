# Trash Hunters (v2)

Projeto e Prática 2 - Informática para Internet - Campus Igarassu

## Como começar

### Clone o repositório:

```bash
git clone ...
```

### Instale as dependências do projeto

Tanto do PHP (Composer) quanto do Node.js (NPM).

```bash
composer install
npm install
```

### Copie o .env.example

Crie um arquivo chamado `.env` na raiz do projeto e copie o conteúdo do arquivo `.env.example` para ele. Em seguida, configure as variáveis de ambiente conforme necessário.

```bash
cp .env.example .env
```

### Gere uma chave de aplicativo

```bash
php artisan key:generate
```

### Execute as migrações do banco de dados

```bash
php artisan migrate
```

### Executando o projeto

```bash
composer run dev
```