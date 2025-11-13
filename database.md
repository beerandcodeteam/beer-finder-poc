# Database Documentation

## Visão Geral

O Beer Finder é um sistema de catálogo e busca de cervejas que permite que lojas cadastrem suas cervejas e usuários possam encontrá-las. O banco de dados foi projetado para suportar:

- Autenticação de usuários com 2FA
- Gerenciamento de lojas e seus catálogos
- Catálogo de cervejas com informações técnicas detalhadas
- Sistema de busca semântica usando embeddings vetoriais
- Relacionamento many-to-many entre cervejas e lojas
- Upload de imagens polimórficas

## Diagrama de Relacionamentos

```
users (1) ──────< (N) stores
                        │
                        ├──── (1) addresses
                        │
                        ├────< (N) catalog_items
                        │
                        └────< (N) beer_store (N) ────> beers
                                                           │
                                                           └────< (N) beer_embeddings

images (polimórfico) ──> beers, stores
```

## Tabelas Principais

### users

Armazena os usuários do sistema (administradores e donos de lojas).

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| is_admin | boolean | Indica se o usuário é administrador (default: false) |
| name | string | Nome do usuário |
| email | string | Email único do usuário |
| email_verified_at | timestamp | Data de verificação do email |
| password | string | Senha hash do usuário |
| two_factor_secret | text | Segredo para autenticação 2FA |
| two_factor_recovery_codes | text | Códigos de recuperação 2FA |
| two_factor_confirmed_at | timestamp | Data de confirmação do 2FA |
| remember_token | string | Token de "lembrar-me" |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `hasMany`: stores

**Recursos:**
- Autenticação com Laravel Fortify
- Suporte a autenticação de dois fatores (2FA)
- Método `initials()` para obter iniciais do nome

**Usuário padrão:**
- Email: admin@teste.com
- Senha: password
- is_admin: true

---

### beers

Catálogo principal de cervejas com informações técnicas e descritivas.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| name | string | Nome da cerveja |
| tagline | string | Slogan/descrição curta |
| description | text | Descrição detalhada |
| first_brewed_date | date | Data da primeira produção |
| abv | decimal(4,1) | Alcohol By Volume (teor alcoólico) |
| ibu | integer | International Bitterness Unit (amargor) |
| ebc | integer | Escala de cor (0=clara, 80=escura) |
| ph | decimal(3,1) | Índice de acidez |
| volume | integer | Volume em ml |
| ingredients | text | Ingredientes da cerveja |
| brewer_tips | text | Dicas do cervejeiro |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsToMany`: stores (através de beer_store)
- `morphMany`: images
- `morphOne`: coverImage (imagem de capa)

**Casts:**
- first_brewed_date: date
- abv: decimal:2
- ph: decimal:2

---

### stores

Lojas que vendem cervejas cadastradas no sistema.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| user_id | foreignId | ID do usuário proprietário |
| name | string | Nome da loja |
| slug | string | Slug único para URL |
| website | string | Website da loja |
| phone | string(15) | Telefone de contato |
| opening_hours_json | json | Horários de funcionamento |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsTo`: user
- `hasOne`: address
- `belongsToMany`: beers (através de beer_store)
- `hasMany`: catalogItems
- `morphMany`: images
- `morphOne`: coverImage (imagem de capa)

**Casts:**
- opening_hours_json: array

**Constraints:**
- Cascade delete: ao deletar uma loja, todos os registros relacionados são deletados

---

### addresses

Endereços das lojas com suporte a geolocalização.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| store_id | foreignId | ID da loja |
| zip_code | string(20) | CEP |
| street | string | Rua |
| number | string(20) | Número |
| neighborhood | string | Bairro |
| city | string | Cidade |
| state | string(100) | Estado |
| country | string(100) | País (nullable) |
| latitude | decimal(10,7) | Latitude para busca geográfica |
| longitude | decimal(10,7) | Longitude para busca geográfica |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsTo`: store

**Constraints:**
- Cascade delete: ao deletar uma loja, o endereço é deletado

---

### beer_store (Tabela Pivot)

Relacionamento many-to-many entre cervejas e lojas, armazenando informações específicas de preço e disponibilidade.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| beer_id | foreignId | ID da cerveja |
| store_id | foreignId | ID da loja |
| price | integer | Preço em centavos |
| url | string | URL do produto na loja |
| promo_label | string | Label de promoção (nullable) |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsTo`: beer
- `belongsTo`: store

**Constraints:**
- Cascade delete em ambas as chaves estrangeiras

**Modelo personalizado:**
- Usa o modelo `BeerStore` (extends Pivot)
- Inclui campos pivot: price, url, promo_label, deleted_at

---

### catalog_items

Items do catálogo específicos de cada loja (produtos que ainda não foram associados a cervejas do banco principal).

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| store_id | foreignId | ID da loja |
| name | string | Nome do produto |
| url | string | URL do produto |
| description | text | Descrição |
| ingredients | text | Ingredientes |
| price | integer | Preço em centavos |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsTo`: store

**Constraints:**
- Cascade delete: ao deletar uma loja, os items do catálogo são deletados

**Observação:**
- Usado para armazenar produtos antes de serem vinculados a cervejas do catálogo principal

---

### beer_embeddings

Armazena embeddings vetoriais das cervejas para busca semântica usando IA.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| beer_id | foreignId | ID da cerveja |
| text | string | Texto que foi transformado em embedding |
| metadata | json | Metadados adicionais |
| embedding | vector | Vetor de embedding para busca semântica |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `belongsTo`: beer

**Constraints:**
- Cascade delete: ao deletar uma cerveja, os embeddings são deletados

**Uso:**
- Permite busca semântica usando similaridade de vetores
- Integrado com LLMs para recomendações inteligentes

---

### images (Relacionamento Polimórfico)

Sistema de upload de imagens que pode ser associado a múltiplos modelos.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| imageable_type | string | Tipo do modelo (Beer, Store) |
| imageable_id | bigint | ID do modelo |
| path | string | Caminho do arquivo |
| is_cover | boolean | Indica se é imagem de capa (default: false) |
| timestamps | timestamps | created_at, updated_at |

**Relacionamentos:**
- `morphTo`: imageable (Beer, Store)

**Uso:**
- Cervejas podem ter múltiplas imagens
- Lojas podem ter múltiplas imagens
- Cada modelo pode ter uma imagem de capa principal

---

## Tabelas do Sistema

### password_reset_tokens

Tokens para recuperação de senha.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| email | string | Email do usuário (PK) |
| token | string | Token de reset |
| created_at | timestamp | Data de criação |

---

### sessions

Gerenciamento de sessões do Laravel.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | string | ID da sessão (PK) |
| user_id | foreignId | ID do usuário (nullable, indexed) |
| ip_address | string(45) | IP do cliente |
| user_agent | text | User agent do navegador |
| payload | longText | Dados da sessão |
| last_activity | integer | Timestamp da última atividade (indexed) |

---

### jobs

Fila de jobs do Laravel para processamento assíncrono.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| queue | string | Nome da fila (indexed) |
| payload | longText | Dados do job |
| attempts | tinyint | Número de tentativas |
| reserved_at | integer | Timestamp de reserva |
| available_at | integer | Timestamp de disponibilidade |
| created_at | integer | Timestamp de criação |

---

### job_batches

Batches de jobs para processamento em lote.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | string | ID do batch (PK) |
| name | string | Nome do batch |
| total_jobs | integer | Total de jobs |
| pending_jobs | integer | Jobs pendentes |
| failed_jobs | integer | Jobs falhados |
| failed_job_ids | longText | IDs dos jobs falhados |
| options | mediumText | Opções do batch |
| cancelled_at | integer | Data de cancelamento |
| created_at | integer | Data de criação |
| finished_at | integer | Data de conclusão |

---

### failed_jobs

Registro de jobs que falharam.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | bigint | Chave primária |
| uuid | string | UUID único |
| connection | text | Conexão usada |
| queue | text | Fila |
| payload | longText | Dados do job |
| exception | longText | Exceção ocorrida |
| failed_at | timestamp | Data da falha |

---

### cache

Sistema de cache do Laravel.

| Campo | Tipo | Descrição |
|-------|------|-----------|
| key | string | Chave do cache (PK) |
| value | mediumText | Valor armazenado |
| expiration | integer | Timestamp de expiração |

---

## Índices e Performance

### Índices Principais

- `users.email`: UNIQUE
- `sessions.user_id`: INDEX
- `sessions.last_activity`: INDEX
- `jobs.queue`: INDEX
- `failed_jobs.uuid`: UNIQUE

### Foreign Keys com Cascade Delete

Todos os relacionamentos implementam cascade delete:
- stores → user_id
- addresses → store_id
- beer_store → beer_id, store_id
- catalog_items → store_id
- beer_embeddings → beer_id

Isso garante integridade referencial e limpeza automática de dados relacionados.

---

## Recursos Especiais

### 1. Busca Semântica

O sistema usa a tabela `beer_embeddings` para implementar busca semântica avançada:
- Textos são convertidos em vetores usando modelos de IA
- Busca por similaridade vetorial
- Permite encontrar cervejas por descrição natural

### 2. Relacionamento Polimórfico

A tabela `images` usa relacionamento polimórfico para:
- Associar imagens a múltiplos modelos (Beer, Store)
- Flexibilidade para adicionar novos modelos imageable
- Suporte a imagem de capa principal

### 3. Armazenamento de Preços

Preços são armazenados em centavos (integer) para evitar problemas de ponto flutuante:
- R$ 10,50 = 1050
- Permite cálculos precisos
- Converta para decimal na apresentação

### 4. Geolocalização

A tabela `addresses` suporta coordenadas geográficas:
- latitude/longitude para busca por proximidade
- Integração com serviços de mapas
- Busca de lojas próximas ao usuário

---

## Migrations

As migrations estão organizadas cronologicamente em `/database/migrations/`:

1. `0001_01_01_000000_create_users_table.php` - Usuários e autenticação
2. `0001_01_01_000001_create_cache_table.php` - Sistema de cache
3. `0001_01_01_000002_create_jobs_table.php` - Sistema de filas
4. `2025_09_22_145432_add_two_factor_columns_to_users_table.php` - 2FA
5. `2025_11_05_133819_create_beers_table.php` - Catálogo de cervejas
6. `2025_11_05_135307_create_images_table.php` - Sistema de imagens
7. `2025_11_05_135603_create_stores_table.php` - Lojas
8. `2025_11_05_135834_create_addresses_table.php` - Endereços
9. `2025_11_05_141359_create_beer_store_table.php` - Pivot beer-store
10. `2025_11_05_142011_create_catalog_items_table.php` - Items de catálogo
11. `2025_11_07_204624_create_beer_embeddings_table.php` - Embeddings vetoriais

---

## Convenções

### Nomenclatura
- Tabelas: plural snake_case (users, beers, catalog_items)
- Colunas: snake_case (first_brewed_date, opening_hours_json)
- Foreign keys: singular_id (user_id, store_id)

### Timestamps
- Todas as tabelas principais usam timestamps (created_at, updated_at)
- Tabelas do sistema usam integer timestamps para performance

### Soft Deletes
- Atualmente não implementado nas tabelas principais
- Usa cascade delete para integridade referencial

---

## Seeders e Factories

Cada modelo possui factory correspondente em `/database/factories/`:
- UserFactory
- BeerFactory
- StoreFactory
- AddressFactory
- CatalogItemFactory
- ImageFactory

Use os seeders para popular o banco com dados de teste:

```bash
php artisan db:seed
```

---

## Considerações de Desenvolvimento

### Ao Adicionar Novas Tabelas

1. Crie a migration usando `php artisan make:migration`
2. Defina foreign keys com cascade delete apropriado
3. Adicione índices para campos frequentemente consultados
4. Crie o modelo com relacionamentos
5. Crie factory e seeder correspondentes
6. Atualize esta documentação

### Performance

- Use eager loading para evitar N+1 queries
- Considere adicionar índices para campos de busca
- Cache de queries frequentes
- Pagination para listagens grandes

### Segurança

- Validação de dados em Form Requests
- Proteção contra SQL Injection (use Eloquent)
- Sanitização de uploads de imagens
- Autorização via Gates e Policies
