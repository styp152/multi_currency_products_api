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

- `GET /api/v1/products`
- `POST /api/v1/products`
- `GET /api/v1/products/{id}`
- `PUT /api/v1/products/{id}`
- `DELETE /api/v1/products/{id}`
- `GET /api/v1/products/{id}/prices`
- `POST /api/v1/products/{id}/prices`

## Capacidades adicionales

- Paginacion en listados con `per_page`
- Filtros de productos por `search`, `currency_id`, `min_price`, `max_price`
- Ordenamiento de productos por `id`, `name`, `price`, `created_at`
- Filtro de precios por `currency_id`
- Errores de API estandarizados en JSON
- Validacion para impedir monedas duplicadas o igual a la moneda base del producto
- El endpoint de precios expone el `base_price` por separado para reflejar el precio almacenado en `products`
- Versionado de API con prefijo `v1`
- Rate limiting explicito para la API
- Separacion de responsabilidades con clases `Action` y `Query`
- `X-Request-Id` en respuestas para trazabilidad y debugging
- Pipeline de CI con pruebas y code style en GitHub Actions

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
GET /api/v1/products?search=Coffee&currency_id=1&min_price=10&max_price=100&sort_by=name&sort_direction=asc&per_page=10
```

## Datos iniciales

El seeder crea estas divisas:

- `USD`
- `EUR`
- `COP`
- `MXN`

## Ejecutar pruebas

```bash
php artisan test
```

## Documentacion entregable

- Swagger/OpenAPI: [docs/openapi.yaml](docs/openapi.yaml)
- Postman: [docs/postman_collection.json](docs/postman_collection.json)
- Insomnia: [docs/insomnia_collection.json](docs/insomnia_collection.json)
- Documento tecnico: [docs/documento.md](docs/documento.md)

## Notas tecnicas

- La API valida entrada con `FormRequest`.
- La capa HTTP delega consultas y creacion a clases dedicadas para reducir acoplamiento en controladores.
- Se usan claves foraneas y restriccion unica en `product_prices` para evitar duplicar precio por moneda dentro del mismo producto.
- `GET /api/v1/products/{id}/prices` devuelve precios adicionales en `data` y el precio base en `base_price`, evitando mezclar dos origenes distintos dentro de la misma coleccion.
- La API devuelve errores JSON consistentes para `404`, `422` y errores internos.
- Las respuestas API incluyen `X-Request-Id` y los errores JSON exponen `request_id`.
- Los listados devuelven metadatos de paginacion.
- La API esta versionada en `/api/v1` y protegida con `throttle:api`.
- El repositorio incluye CI en [`.github/workflows/ci.yml`](.github/workflows/ci.yml).
