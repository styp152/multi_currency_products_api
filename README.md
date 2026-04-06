# Multi Currency Products API

API RESTful en Laravel para gestionar productos con precio base y precios en multiples divisas.

## Stack

- Laravel 13
- PHP 8.5
- SQLite por defecto para levantar rapido
- Eloquent ORM
- Pruebas feature con PHPUnit

## Requisitos cubiertos

- CRUD de productos
- Registro de precios por producto en diferentes divisas
- Respuestas JSON
- Persistencia con Eloquent
- Documentacion en Swagger/OpenAPI, Postman, Insomnia y documento tecnico

## Modelo

### `currencies`

- `id`
- `name`
- `symbol`
- `exchange_rate`

### `products`

- `id`
- `name`
- `description`
- `price`
- `currency_id`
- `tax_cost`
- `manufacturing_cost`

### `product_prices`

- `id`
- `product_id`
- `currency_id`
- `price`

## Endpoints

- `GET /api/products`
- `POST /api/products`
- `GET /api/products/{id}`
- `PUT /api/products/{id}`
- `DELETE /api/products/{id}`
- `GET /api/products/{id}/prices`
- `POST /api/products/{id}/prices`

## Setup

```bash
cp .env.example .env
touch database/database.sqlite
/opt/homebrew/bin/php artisan key:generate
/opt/homebrew/bin/php artisan migrate --seed
/opt/homebrew/bin/php artisan serve
```

Base URL local:

```text
http://127.0.0.1:8000
```

## Datos iniciales

El seeder crea estas divisas:

- `USD`
- `EUR`
- `COP`
- `MXN`

## Ejecutar pruebas

```bash
/opt/homebrew/bin/php artisan test
```

## Documentacion entregable

- Swagger/OpenAPI: [docs/openapi.yaml](docs/openapi.yaml)
- Postman: [docs/postman_collection.json](docs/postman_collection.json)
- Insomnia: [docs/insomnia_collection.json](docs/insomnia_collection.json)
- Documento tecnico: [docs/documento.md](docs/documento.md)

## Notas tecnicas

- La API valida entrada con `FormRequest`.
- Se usan claves foraneas y restriccion unica en `product_prices` para evitar duplicar precio por moneda dentro del mismo producto.
- El proyecto queda sin autenticacion porque no fue parte del enunciado. Si quieres destacar en la siguiente iteracion, lo siguiente natural es agregar auth, versionado, filtros, paginacion y generacion automatica de Swagger desde anotaciones o atributos.
