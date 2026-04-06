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

## Capacidades adicionales

- Paginacion en listados con `per_page`
- Filtros de productos por `search`, `currency_id`, `min_price`, `max_price`
- Ordenamiento de productos por `id`, `name`, `price`, `created_at`
- Filtro de precios por `currency_id`
- Errores de API estandarizados en JSON
- Validacion para impedir monedas duplicadas o igual a la moneda base del producto

## Setup

```bash
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Base URL local:

```text
http://127.0.0.1:8000
```

Ejemplo de listado con filtros:

```text
GET /api/products?search=Coffee&currency_id=1&min_price=10&max_price=100&sort_by=name&sort_direction=asc&per_page=10
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
- La API devuelve errores JSON consistentes para `404`, `422` y errores internos.
- Los listados devuelven metadatos de paginacion.
- El proyecto queda sin autenticacion porque no fue parte del enunciado. Si quieres destacar aun mas, el siguiente paso natural es agregar auth, versionado y CI.
