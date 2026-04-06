# Documento Tecnico

## Objetivo

Construir una API RESTful en Laravel para gestionar productos y sus precios en distintas divisas.

## Decisiones principales

- Se uso `SQLite` para tener una base portable y lista para evaluar sin configuracion adicional.
- Se uso `Eloquent` con relaciones explicitas entre `currencies`, `products` y `product_prices`.
- Se separo la validacion en `FormRequest` para mantener controladores delgados.
- Se usan `Resources` para normalizar la salida JSON.
- Se versiono la API con prefijo `/api/v1`.
- Se extrajo logica de consulta y escritura a clases `Query` y `Action`.

## Estructura

- `Currency`: catalogo de monedas y tasa de cambio referencial.
- `Product`: producto con precio base en una moneda principal.
- `ProductPrice`: precio del producto en otras monedas.

## Reglas de negocio implementadas

- Un producto debe tener nombre, descripcion, precio base, moneda base, costo de impuestos y costo de fabricacion.
- Un producto puede tener multiples precios adicionales.
- Un producto no puede tener dos registros de precio para la misma moneda.
- Un precio adicional no puede usar la misma moneda base del producto.
- Al eliminar un producto se eliminan sus precios asociados.

## Capacidades de consulta

- Busqueda de productos por texto en nombre y descripcion.
- Filtro de productos por moneda base.
- Filtro de productos por rango de precio.
- Ordenamiento configurable.
- Paginacion configurable.
- Filtro de precios por moneda en el endpoint de precios del producto.
- Rate limiting por IP para la API.

## Seguridad aplicada

- Validacion de payload con `FormRequest`.
- Restricciones de integridad con claves foraneas.
- Restriccion unica compuesta en `product_prices`.
- Respuesta JSON consistente sin exponer informacion innecesaria del modelo.
- Manejo uniforme de errores `404`, `422` y `500` para la API.
- Throttling de API con `RateLimiter`.

## Pruebas

Se incluyen pruebas feature para:

- Listado de productos
- Creacion de producto
- Consulta de producto individual
- Actualizacion de producto
- Eliminacion de producto
- Listado de precios por producto
- Creacion de precio por moneda
- Filtros y paginacion
- Validaciones y errores de negocio
- Respuesta JSON para recursos no encontrados
- Rate limiting

## Mejoras siguientes para destacar

- Autenticacion con Sanctum
- Generacion automatica de Swagger desde codigo
- CI con pruebas y analisis estatico
