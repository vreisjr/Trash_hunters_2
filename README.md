# Trash Hunters (v2)

Projeto e Prática 2 - Informática para Internet - Campus Igarassu

Gerenciamento de Projetos em: https://app.rollcake.com.br/projects/f7d35077-059a-4490-a3cc-d853aad6e506

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

### Insira os dados iniciais das categorias no banco de dados

```bash
php artisan db:seed --class=CategoriaSeeder
```

### Executando o projeto

```bash
composer run dev
```

## Dados de exemplo (opcional)

Para popular o banco de desenvolvimento com usuários, posts, comentários e respostas falsos:

```bash
# Posts do marketplace (com categorias, comentários e respostas)
php artisan db:seed --class=MarketplaceSeeder

# Posts do feed (com categorias, comentários e respostas)
php artisan db:seed --class=FeedSeeder
```

Cada execução cria mais 5 usuários e 8 posts, sem apagar os dados anteriores.

Para recriar o banco do zero com o seeder principal:

```bash
php artisan migrate:fresh --seed
```

> Atenção: `migrate:fresh` apaga todos os dados do banco. Use apenas em desenvolvimento.

## Testes

```bash
# Todos os testes
php artisan test

# Um arquivo específico
php artisan test tests/Feature/FactoriesTest.php

# Testes + lint + análise de tipos (o mesmo que roda no CI)
composer test
```

## Factories

As factories ficam em `database/factories/` e geram dados falsos para os testes e os seeders.

| Factory | States disponíveis |
|---|---|
| `UserFactory` | `unverified()`, `withTwoFactor()`, `admin()`, `premium()` |
| `PostFactory` | `marketplace()`, `paid($preco)`, `edited()` |
| `ComentarioFactory` | `resposta($comentarioPai)` |
| `CategoriaFactory` | - |
| `ColetaFactory` | - |

Exemplos de uso nos testes:

```php
$admin = User::factory()->admin()->create();
$post  = Post::factory()->paid(40)->create();
$pai   = Comentario::factory()->create();
Comentario::factory()->count(3)->resposta($pai)->create();
```

## Qualidade de código

```bash
composer lint         # formata o código (Pint)
composer lint:check   # apenas verifica o formato
composer types:check  # análise estática (PHPStan)
```
