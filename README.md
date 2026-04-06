# Multi Currency Products API

[![CI](https://github.com/styp152/multi_currency_products_api/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/styp152/multi_currency_products_api/actions/workflows/ci.yml)

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
- El endpoint de precios incluye el precio base dentro de `data` con el mismo shape y el flag `is_base_price`
- Versionado de API con prefijo `v1`
- Rate limiting explicito para la API
- Separacion de responsabilidades con clases `Action` y `Query`
- Proteccion de endpoints de escritura con `X-API-Key`
- `X-Request-Id` en respuestas para trazabilidad y debugging
- Pipeline de CI con pruebas y code style en GitHub Actions
- Entorno reproducible con Docker y Docker Compose

## Setup

```bash
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Alternativa con Docker:

```bash
docker compose up --build
```

Al levantar con Docker, el contenedor ejecuta automaticamente:

- `php artisan migrate --seed --force`
- `php artisan serve --host=0.0.0.0 --port=8000`

Para proteger escrituras en local, configura una llave:

```bash
API_WRITE_KEY=your-secure-key
```

Base URL local:

```text
http://127.0.0.1:8000
```

Ejemplo de listado con filtros:

```text
GET /api/v1/products?search=Coffee&currency_id=1&min_price=10&max_price=100&sort_by=name&sort_direction=asc&per_page=10
```

Ejemplo de escritura autenticada:

```bash
curl -X POST http://127.0.0.1:8000/api/v1/products \
  -H "Content-Type: application/json" \
  -H "X-API-Key: your-secure-key" \
  -d '{
    "name": "Premium Coffee",
    "description": "Single origin coffee beans.",
    "price": 29.99,
    "currency_id": 1,
    "tax_cost": 3.5,
    "manufacturing_cost": 12.1
  }'
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
- La lectura de detalle de producto y la respuesta del listado de precios tambien estan encapsuladas en capas dedicadas.
- Se usan claves foraneas y restriccion unica en `product_prices` para evitar duplicar precio por moneda dentro del mismo producto.
- `GET /api/v1/products/{id}/prices` incluye el precio base como primer item de `data` con `is_base_price=true`, manteniendo un contrato estable para una futura unificacion del modelo.
- La API devuelve errores JSON consistentes para `404`, `422` y errores internos.
- Las operaciones de escritura requieren `X-API-Key` cuando `API_WRITE_KEY` esta configurada.
- Las respuestas API incluyen `X-Request-Id` y los errores JSON exponen `request_id`.
- Los listados devuelven metadatos de paginacion.
- La API esta versionada en `/api/v1` y protegida con `throttle:api`.
- El repositorio incluye CI en [`.github/workflows/ci.yml`](.github/workflows/ci.yml).
- El CI valida `composer.json`, revisa `route:list` y prueba `config:cache` como smoke checks adicionales.
