# API REST - Planes de Cuotas (TP Parte 3)

API REST pública que expone la información del sistema de **Planes de Cuotas**
desarrollado en el TP Parte 2, para que sistemas de terceros puedan
integrarse (por ejemplo, una app mobile que consulte el estado de las
cuotas de un cliente).

Comparte la base de datos (`db_planes_cuotas`) con el repositorio del TP
Parte 2, pero es un proyecto PHP independiente, sin frontend ni
dependencia de ese código.

## Entidad principal: Cuota

| Campo         | Tipo                | Descripción                                  |
|---------------|---------------------|-----------------------------------------------|
| id_cuota      | int (autogenerado)  | Identificador único                           |
| id_plan       | int                 | FK a `plan_de_cuotas` (categoría)             |
| id_cliente    | int                 | FK a `cliente`                                |
| nro_cuota     | int                 | Número de cuota dentro del plan               |
| monto         | decimal             | Monto de la cuota                             |
| estado_pago   | 0 / 1               | 0 = pendiente, 1 = pagada                     |
| imagen_url    | string (opcional)   | URL de imagen asociada                        |

También se expone, de forma auxiliar y solo lectura, el recurso
`/api/planes` (la categoría a la que pertenece cada cuota).

## Instalación

1. Importar `db/db_planes_cuotas.sql` (es la misma base que usa el TP
   Parte 2; el script usa `CREATE TABLE IF NOT EXISTS` e
   `INSERT IGNORE`, por lo que no rompe los datos existentes si la base
   ya estaba creada).
2. Configurar las credenciales de conexión en `config.php`.
3. Levantar el proyecto en un servidor con `mod_rewrite` habilitado
   (Apache/XAMPP), apuntando el DocumentRoot a esta carpeta.
4. Probar con Postman usando como base URL: `http://localhost/`

## Autenticación

Los endpoints `POST` y `PUT` requieren un token enviado por header:

```
Authorization: Bearer TP2026-DEMO-TOKEN-12345
```

Ese token de prueba ya viene cargado en la tabla `api_token` por el
script SQL. Si no se envía el header, o el token no existe en la
tabla, la API responde `401 Unauthorized`.

Los `GET` son públicos y no requieren token.

## Endpoints

### 1. Listar cuotas

```
GET /api/cuotas
```

Query params (todos opcionales):

| Param          | Descripción                                                              | Ejemplo                |
|----------------|---------------------------------------------------------------------------|-------------------------|
| `sort`         | Campo por el cual ordenar. Default: `id_cuota`. Acepta cualquier campo de la tabla (`id_cuota`, `id_plan`, `id_cliente`, `nro_cuota`, `monto`, `estado_pago`, `imagen_url`) | `sort=monto` |
| `dir`          | `asc` o `desc`. Default: `asc`                                            | `dir=desc`              |
| `filter_field` | Campo por el cual filtrar (debe enviarse junto a `filter_value`)          | `filter_field=estado_pago` |
| `filter_value` | Valor a buscar en `filter_field` (igualdad exacta)                        | `filter_value=0`        |
| `page`         | Número de página (junto con `limit`)                                      | `page=1`                |
| `limit`        | Cantidad de resultados por página                                         | `limit=10`              |

Ejemplos:

```
GET /api/cuotas
GET /api/cuotas?sort=monto&dir=desc
GET /api/cuotas?filter_field=estado_pago&filter_value=0
GET /api/cuotas?page=1&limit=2
GET /api/cuotas?sort=nro_cuota&dir=asc&filter_field=id_plan&filter_value=1&page=1&limit=5
```

Respuesta `200 OK` (sin paginar):

```json
{
  "data": [
    { "id_cuota": 1, "id_plan": 1, "id_cliente": 1, "nro_cuota": 1, "monto": "15000.00", "estado_pago": 1, "imagen_url": null }
  ],
  "total": 4
}
```

Respuesta `200 OK` (paginando con `page` y `limit`):

```json
{
  "data": [ /* ... */ ],
  "pagination": { "page": 1, "limit": 2, "total": 4, "total_pages": 2 }
}
```

Errores: `400 Bad Request` si `filter_field` no es una columna válida,
si se manda solo `page` o solo `limit`, o si esos valores son menores a 1.

---

### 2. Obtener una cuota por ID

```
GET /api/cuotas/{id}
```

Ejemplo: `GET /api/cuotas/1`

Respuesta `200 OK`:

```json
{ "id_cuota": 1, "id_plan": 1, "id_cliente": 1, "nro_cuota": 1, "monto": "15000.00", "estado_pago": 1, "imagen_url": null }
```

Respuesta `404 Not Found` si el id no existe:

```json
{ "error": "No existe una cuota con id 99" }
```

---

### 3. Crear una cuota

```
POST /api/cuotas
Authorization: Bearer TP2026-DEMO-TOKEN-12345
Content-Type: application/json
```

Body:

```json
{
  "id_plan": 1,
  "id_cliente": 1,
  "nro_cuota": 3,
  "monto": 15000.00,
  "estado_pago": 0,
  "imagen_url": null
}
```

Respuesta `201 Created`: devuelve la cuota recién creada (con su `id_cuota`).

Errores:
- `400 Bad Request`: faltan campos obligatorios, `monto`/`nro_cuota`
  inválidos, o `id_plan`/`id_cliente` que no existen.
- `401 Unauthorized`: falta o es inválido el token.

---

### 4. Modificar una cuota

```
PUT /api/cuotas/{id}
Authorization: Bearer TP2026-DEMO-TOKEN-12345
Content-Type: application/json
```

Body: mismo formato que el `POST`.

Ejemplo: `PUT /api/cuotas/1`

```json
{
  "id_plan": 1,
  "id_cliente": 1,
  "nro_cuota": 1,
  "monto": 16000.00,
  "estado_pago": 1,
  "imagen_url": null
}
```

Respuesta `200 OK`: devuelve la cuota actualizada.

Errores: `400 Bad Request` (datos inválidos), `401 Unauthorized` (sin
token), `404 Not Found` (el id no existe).

---

### 5. Listar planes (auxiliar, solo lectura)

```
GET /api/planes
GET /api/planes/{id}
```

Devuelve las categorías (`plan_de_cuotas`) para que un cliente externo
pueda armar selects o mostrar el nombre del plan sin acceder
directamente a la base de datos.

## Códigos de respuesta usados

| Código | Cuándo                                                  |
|--------|-----------------------------------------------------------|
| 200    | Operación exitosa (GET, PUT)                               |
| 201    | Recurso creado (POST)                                      |
| 400    | Datos inválidos / faltantes en la request                  |
| 401    | Falta o es inválido el token en POST/PUT                   |
| 404    | El recurso solicitado no existe                            |

## División de responsabilidades del equipo

| Integrante | Responsabilidad                                                        |
|------------|---------------------------------------------------------------------------|
| Miembro A  | `GET /api/cuotas` (listado + orden por un campo), `PUT /api/cuotas/{id}`, ordenamiento por cualquier campo (opcional) |
| Miembro B  | `GET /api/cuotas/{id}`, `POST /api/cuotas`, filtrado por campo (opcional) |
| Ambos      | Paginado (opcional), autenticación por token (opcional)                  |
