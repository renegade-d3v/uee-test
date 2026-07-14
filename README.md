# UEE API

REST API for Ukrainian Energy Exchange — company data management with full version history.

## Stack

- **Laravel 12** + PHP 8.4
- **SQLite** (persisted via Docker volume)
- **Tailwind CSS v4** + Vite
- **Docker** — nginx + php-fpm

## Getting started

```bash
git clone [<repo-url>](https://github.com/renegade-d3v/uee-test) uee
cd uee
cp .env.example .env
```

### With Docker (recommended)

```bash
# First run — builds image, runs migrations automatically
docker compose up -d --build

# Subsequent starts
docker compose up -d

# Stop
docker compose down
```

App is available at **http://localhost:8080**  
Swagger UI: **http://localhost:8080/api/documentation**

### Without Docker

```bash
composer setup   # install deps, copy .env, generate key, migrate, npm install & build
composer dev     # start php artisan serve + vite concurrently
```

## API endpoints

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/api/company` | Create or update a company |
| `GET` | `/api/company/{edrpou}/versions` | Get full version history of a company |

### POST /api/company

**Request body:**
```json
{
  "name": "ТОВ Українська енергетична біржа",
  "edrpou": "37027819",
  "address": "01001, Україна, м. Київ, вул. Хрещатик, 44"
}
```

**Response:**
```json
{ "status": "created", "company_id": 1, "version": 1 }
```

Possible `status` values: `created`, `updated`, `duplicate`.

### GET /api/company/{edrpou}/versions

```json
{
  "company_id": 1,
  "edrpou": "37027819",
  "versions": [
    {
      "version": 1,
      "old_data": null,
      "new_data": { "name": "...", "edrpou": "...", "address": "..." },
      "changes": null,
      "created_at": "2026-07-14T10:00:00.000000Z"
    }
  ]
}
```

## Commands

All commands can also be run inside the Docker container:

```bash
docker compose exec app php artisan <command>
```

### Tests

```bash
composer test                  # full test suite (Pest + type coverage + lint)
composer test:lint             # dry-run lint check (non-zero exit if changes needed)

# Inside container
docker compose exec app php artisan test
docker compose exec app php artisan test --filter CompanyStoreTest
```

### Linting

```bash
composer lint        # fix code style with Laravel Pint
composer test:lint   # check only, do not modify files
```

### API documentation

```bash
composer doc         # regenerate OpenAPI spec (swagger.json)
```

Then open **http://localhost:8080/api/documentation**.
