# Contac API

API REST en Laravel 13 para gestionar contactos personales. Cada usuario inicia sesión con un token de Sanctum y solo puede ver, editar o eliminar sus propios contactos.

Proyecto del programa ADSO (SENA).

## Tecnologías

PHP 8.4, Laravel 13, MySQL 8, Laravel Sanctum, Pest.

## Instalación

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Crear antes las bases de datos en MySQL:

```sql
CREATE DATABASE db_contac_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE db_contac_api_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Usuarios de prueba que crea el seeder (contraseña `password`):

| Correo               | Contactos |
|----------------------|-----------|
| admin@contactos.test | 5         |
| ana@contactos.test   | 5         |

## Pruebas

Las pruebas usan la base `db_contac_api_test` (ver `phpunit.xml`), nunca la de desarrollo.

```bash
php artisan test
```

## Base de datos

**users**: id, first_name (obligatorio), last_name (opcional), email (obligatorio, único), password (hash), phone_number (opcional, único), created_at, updated_at.

**contacts**: id, user_id (FK a users.id), first_name (obligatorio), last_name (opcional), phone_number (obligatorio, único), email (obligatorio, único), address (obligatorio), avatar_url (opcional), created_at, updated_at, deleted_at (borrado lógico).

## Endpoints

Base: `http://127.0.0.1:8000/api/v1`. Enviar siempre `Accept: application/json`. Las rutas con token usan `Authorization: Bearer <token>`.

| Método | Ruta              | Descripción                              | Token |
|--------|-------------------|------------------------------------------|-------|
| GET    | /health           | Estado del servicio                      | No    |
| POST   | /auth/register    | Registrar usuario y obtener token        | No    |
| POST   | /auth/login       | Iniciar sesión (email, password, device_name) | No |
| POST   | /auth/logout      | Cerrar sesión (revoca el token actual)   | Sí    |
| GET    | /auth/me          | Perfil del usuario                       | Sí    |
| PUT    | /auth/me          | Actualizar perfil del usuario            | Sí    |
| GET    | /contacts         | Listar mis contactos (paginado)          | Sí    |
| POST   | /contacts         | Crear contacto                           | Sí    |
| GET    | /contacts/{id}    | Perfil de un contacto                    | Sí    |
| PUT    | /contacts/{id}    | Actualizar contacto                      | Sí    |
| DELETE | /contacts/{id}    | Eliminar contacto (soft delete)          | Sí    |

## Reglas y restricciones

| Situación                                   | Respuesta |
|---------------------------------------------|-----------|
| Sin token o token revocado                  | 401 `{"message": "No has iniciado sesión…", "login_url": ".../auth/login"}` |
| Contacto de otro usuario                    | 403 `{"message": "No tienes permiso para acceder a este contacto."}` |
| Contacto que no existe o ya fue eliminado   | 404 `{"message": "El recurso solicitado no existe."}` |
| Ruta que no existe                          | 404 `{"message": "La ruta solicitada no existe."}` |
| Datos inválidos, correo o teléfono repetido | 422 con `errors` campo por campo |
| Más de 5 logins por minuto                  | 429 |

El correo y el teléfono de un contacto no se pueden repetir entre contactos, y el correo y el teléfono de un usuario no se pueden repetir entre usuarios. Al eliminar un contacto solo se llena `deleted_at`: el registro sigue en la base pero ya no aparece en la API.
