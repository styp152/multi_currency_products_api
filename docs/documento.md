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
- Se encapsulo la lectura de detalle y la respuesta del listado de precios en clases dedicadas para mantener controladores delgados.
- Se agrego `request_id` para trazabilidad de solicitudes.
- Se incorporo CI con validacion automatica de estilo y pruebas.
- Se protegieron operaciones de escritura con una API key configurable por entorno.
- Se agrego un entorno Docker reproducible para evaluacion rapida.

## Estructura

- `Currency`: catalogo de monedas y tasa de cambio referencial.
- `Product`: producto con precio base en una moneda principal.
- `ProductPrice`: precio del producto en otras monedas.

## Observacion de diseno actual

El modelo actual guarda el precio base en `products` y los precios adicionales en `product_prices`.
Eso funciona para la prueba, pero introduce una asimetria: no existe una sola fuente de verdad para todos los precios del producto.

Para hacer explicito ese comportamiento sin romper el contrato del listado de precios adicionales:

- `GET /api/v1/products/{id}/prices` devuelve el precio base y los precios adicionales dentro de la misma lista `data`.
- El precio base usa el mismo shape de respuesta y se distingue con `is_base_price=true`.
- La API rechaza intentos de duplicar exactamente el precio base dentro de `product_prices`.

## Reglas de negocio implementadas

- Un producto debe tener nombre, descripcion, precio base, moneda base, costo de impuestos y costo de fabricacion.
- Un producto puede tener multiples precios adicionales.
- Un producto no puede tener dos registros de precio para la misma moneda.
- Un precio adicional no puede usar la misma moneda base del producto.
- Un precio adicional no puede duplicar exactamente el precio base ya almacenado en `products`.
- Al eliminar un producto se eliminan sus precios asociados.

## Capacidades de consulta

- Busqueda de productos por texto en nombre y descripcion.
- Filtro de productos por moneda base.
- Filtro de productos por rango de precio.
- Ordenamiento configurable.
- Paginacion configurable.
- Filtro de precios por moneda en el endpoint de precios del producto.
- Rate limiting por IP para la API.
- Correlation ID por request mediante cabecera `X-Request-Id`.
- Endpoints de escritura protegidos con cabecera `X-API-Key`.
- Entorno reproducible con Docker Compose.

## Seguridad aplicada

- Validacion de payload con `FormRequest`.
- Restricciones de integridad con claves foraneas.
- Restriccion unica compuesta en `product_prices`.
- Respuesta JSON consistente sin exponer informacion innecesaria del modelo.
- Manejo uniforme de errores `404`, `422` y `500` para la API.
- Throttling de API con `RateLimiter`.
- Trazabilidad basica con `request_id` en cabeceras y errores JSON.
- Control simple de acceso a escrituras sin acoplar la prueba a paquetes externos.
- Smoke checks de bootstrap y configuracion en CI.

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
- Propagacion de `request_id`
- Seguridad de escrituras
- Docker y smoke checks operativos

## Mejoras siguientes para destacar

- Autenticacion con Sanctum
- Generacion automatica de Swagger desde codigo
- CI con pruebas y analisis estatico

## Propuesta de mejora del modelo de datos

Si el sistema creciera, la mejor normalizacion seria evitar que el precio base viva en una tabla distinta al resto de precios.

Propuesta:

- Mover todos los precios a una sola tabla `product_prices`.
- Marcar uno de ellos como precio base con una columna booleana `is_base` o con una referencia `base_product_price_id` en `products`.
- Mantener una restriccion unica para `product_id + currency_id`.
- Resolver el precio principal del producto desde la misma fuente que los precios alternos.

Beneficios:

- Una sola fuente de verdad para precios.
- Menos ambiguedad al listar precios.
- Menos validaciones especiales entre `products` y `product_prices`.
- Facilita auditoria, historico y futuras reglas de negocio.
