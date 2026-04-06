# Documento Tecnico

## Objetivo

Construir una API RESTful en Laravel para gestionar productos y sus precios en distintas divisas.

## Decisiones principales

- Se uso `SQLite` para tener una base portable y lista para evaluar sin configuracion adicional.
- Se uso `Eloquent` con relaciones explicitas entre `currencies`, `products` y `product_prices`.
- Se separo la validacion en `FormRequest` para mantener controladores delgados.
- Se usan `Resources` para normalizar la salida JSON.

## Estructura

- `Currency`: catalogo de monedas y tasa de cambio referencial.
- `Product`: producto con precio base en una moneda principal.
- `ProductPrice`: precio del producto en otras monedas.

## Reglas de negocio implementadas

- Un producto debe tener nombre, descripcion, precio base, moneda base, costo de impuestos y costo de fabricacion.
- Un producto puede tener multiples precios adicionales.
- Un producto no puede tener dos registros de precio para la misma moneda.
- Al eliminar un producto se eliminan sus precios asociados.

## Seguridad aplicada

- Validacion de payload con `FormRequest`.
- Restricciones de integridad con claves foraneas.
- Restriccion unica compuesta en `product_prices`.
- Respuesta JSON consistente sin exponer informacion innecesaria del modelo.

## Pruebas

Se incluyen pruebas feature para:

- Listado de productos
- Creacion de producto
- Consulta de producto individual
- Actualizacion de producto
- Eliminacion de producto
- Listado de precios por producto
- Creacion de precio por moneda

## Mejoras siguientes para destacar

- Autenticacion con Sanctum
- Paginacion, filtros y ordenamiento
- Manejo global de errores con formato estandarizado
- Generacion automatica de Swagger desde codigo
- CI con pruebas y analisis estatico
